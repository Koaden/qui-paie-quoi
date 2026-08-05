<?php

declare(strict_types=1);

namespace Domain\UseCase\Member\Edit;

use Domain\ReadModel\Member;

final readonly class Input
{
    /** @param non-empty-string $email */
    public function __construct(
        public Member $member,
        public string $name,
        public string $email,
        public string $plainPassword,
    ) {
    }
}
