<?php

declare(strict_types=1);

namespace Domain\UseCase\Participant\Edit;

use Domain\Model\Participant;

final readonly class Output
{
    public function __construct(
        public readonly Participant $participant,
    ) {
    }
}
