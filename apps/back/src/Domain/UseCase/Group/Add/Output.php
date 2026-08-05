<?php

declare(strict_types=1);

namespace Domain\UseCase\Group\Add;

use Domain\Model\Group;

final readonly class Output
{
    public function __construct(
        public Group $group,
    ) {
    }
}
