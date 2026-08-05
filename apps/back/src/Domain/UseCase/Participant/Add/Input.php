<?php

declare(strict_types=1);

namespace Domain\UseCase\Participant\Add;

use Domain\ReadModel\Group;
use Domain\ReadModel\Member;

final readonly class Input
{
    public function __construct(
        public Member $member,
        public string $name,
        public Group $group,
    ) {
    }
}
