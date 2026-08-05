<?php

declare(strict_types=1);

namespace Domain\UseCase\Participant\Edit;

use Domain\Collection\ParticipantCollection;
use Domain\Exception\MemberPermissionRequired;
use Domain\Exception\ParticipantDoesntExist;

final readonly class Handler
{
    public function __construct(
        private readonly ParticipantCollection $participantCollection,
    ) {
    }

    public function __invoke(Input $input): Output
    {
        if (!($participant = $this->participantCollection->findOneById($input->id))) {
            throw new ParticipantDoesntExist();
        }

        if (null !== $participant->getMember() && $participant->getMember()->getId() !== $input->member->id) {
            throw new MemberPermissionRequired();
        }

        $participant->setName($input->name);

        $this->participantCollection->add($participant);

        return new Output($participant);
    }
}
