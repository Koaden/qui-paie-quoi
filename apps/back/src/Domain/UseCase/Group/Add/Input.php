<?php

declare(strict_types=1);

namespace Domain\UseCase\Group\Add;

use Domain\ReadModel\Member;

final readonly class Input
{
    public function __construct(
        public string $name,
        public Member $owner,
        public ?string $description = null,
    ) {
    }
}
