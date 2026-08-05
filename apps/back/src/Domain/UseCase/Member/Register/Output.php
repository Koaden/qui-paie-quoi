<?php

declare(strict_types=1);

namespace Domain\UseCase\Member\Register;

use Domain\Model\Member;

final readonly class Output
{
    public function __construct(
        public Member $member,
    ) {
    }
}
