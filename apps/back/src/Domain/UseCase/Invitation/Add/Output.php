<?php

declare(strict_types=1);

namespace Domain\UseCase\Invitation\Add;

use Domain\Model\Invitation;

final readonly class Output
{
    public function __construct(
        public Invitation $invitation,
    ) {
    }
}
