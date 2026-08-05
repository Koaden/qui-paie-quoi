<?php

declare(strict_types=1);

namespace Domain\Query\Participant\GetSingle;

use Domain\Collection\ParticipantCollection;
use Domain\Exception\ParticipantDoesntExist;
use Domain\ReadModel\Participant;

final readonly class Handler
{
    public function __construct(
        private ParticipantCollection $participantCollection,
    ) {
    }

    public function __invoke(Query $query): Participant
    {
        if (!($participantModel = $this->participantCollection->findOneById($query->id))) {
            throw new ParticipantDoesntExist();
        }

        return Participant::fromModel($participantModel);
    }
}
