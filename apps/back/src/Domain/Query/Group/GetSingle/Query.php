<?php

declare(strict_types=1);

namespace Domain\Query\Group\GetSingle;

use Domain\ReadModel\Member;

final readonly class Query
{
    public function __construct(
        public int $id,
        public Member $member,
    ) {
    }
}
