<?php

declare(strict_types=1);

namespace Domain\UseCase\Invitation\Add;

use Domain\Collection\InvitationCollection;
use Domain\Collection\MemberCollection;
use Domain\Collection\ParticipantCollection;
use Domain\Exception\GroupDoesntExist;
use Domain\Exception\ParticipantDoesntExist;
use Domain\Model\Invitation;

final readonly class Handler
{
    public function __construct(
        private InvitationCollection $invitationCollection,
        private ParticipantCollection $participantCollection,
        private MemberCollection $memberCollection,
    ) {
    }

    public function __invoke(Input $input): Output
    {
        if (!($group = $this->memberCollection->findOneGroupById($input->group->id, $input->member))) {
            throw new GroupDoesntExist();
        }

        if (!($participant = $this->participantCollection->findOneById($input->participant->id))) {
            throw new ParticipantDoesntExist();
        }

        $invitation = new Invitation(
            $group,
            $participant,
        );

        $this->invitationCollection->add($invitation);

        return new Output($invitation);
    }
}
