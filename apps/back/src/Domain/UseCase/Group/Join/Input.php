<?php

declare(strict_types=1);

namespace Domain\UseCase\Group\Join;

use Domain\ReadModel\Member;

final readonly class Input
{
    public function __construct(
        public Member $member,
        public string $code,
    ) {
    }
}
