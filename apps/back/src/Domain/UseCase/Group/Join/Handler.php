<?php

declare(strict_types=1);

namespace Domain\UseCase\Group\Join;

use Domain\Collection\GroupCollection;
use Domain\Collection\InvitationCollection;
use Domain\Collection\MemberCollection;
use Domain\Collection\ParticipantCollection;
use Domain\Enum\GroupRole;
use Domain\Exception\InvitationDoesntExist;
use Domain\Exception\MemberDoesntExist;
use Domain\Exception\MemberAlreadyInThisGroup;

final readonly class Handler
{
    public function __construct(
        private InvitationCollection $invitationCollection,
        private ParticipantCollection $participantCollection,
        private GroupCollection $groupCollection,
        private MemberCollection $memberCollection,
    ) {
    }

    public function __invoke(Input $input): Output
    {
        if (!($member = $this->memberCollection->findOneById($input->member->id))) {
            throw new MemberDoesntExist();
        }

        if (!($invitation = $this->invitationCollection->findOneByCode($input->code))) {
            throw new InvitationDoesntExist();
        }

        foreach ($member->getGroups() as $group) {
            if ($group === $invitation->getGroup()) {
                throw new MemberAlreadyInThisGroup();
            }
        }

        $group = $invitation->getGroup();
        $participant = $invitation->getParticipant();

        $member->addGroup($group, GroupRole::VIEWER, $participant);
        $group->removeParticipant($participant);

        $this->participantCollection->remove($participant);
        $this->groupCollection->add($group);
        $this->memberCollection->add($member);

        $this->invitationCollection->remove($invitation);

        return new Output($invitation->getGroup());
    }
}
