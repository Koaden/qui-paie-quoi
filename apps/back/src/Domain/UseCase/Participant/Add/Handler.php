<?php

declare(strict_types=1);

namespace Domain\UseCase\Participant\Add;

use Domain\Collection\GroupCollection;
use Domain\Collection\MemberCollection;
use Domain\Collection\ParticipantCollection;
use Domain\Exception\GroupDoesntExist;
use Domain\Model\Participant;

final readonly class Handler
{
    public function __construct(
        private ParticipantCollection $participantCollection,
        private GroupCollection $groupCollection,
        private MemberCollection $memberCollection,
    ) {
    }

    public function __invoke(Input $input): Output
    {
        if (!($group = $this->memberCollection->findOneGroupById($input->group->id, $input->member))) {
            throw new GroupDoesntExist();
        }

        $participant = new Participant(
            name: $input->name,
        );

        $participant->addGroup($group);
        $group->addParticipant($participant);

        $this->participantCollection->add($participant);
        $this->groupCollection->add($group);

        return new Output($participant);
    }
}
