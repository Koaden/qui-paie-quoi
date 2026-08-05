<?php

declare(strict_types=1);

namespace Domain\Collection;

use Domain\Model\Participant;

interface ParticipantCollection
{
    public function add(Participant $participant): void;

    public function remove(Participant $participant): void;

    public function findOneById(int $id): ?Participant;
}
