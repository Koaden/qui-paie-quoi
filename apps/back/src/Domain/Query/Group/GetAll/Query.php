<?php

declare(strict_types=1);

namespace Domain\Query\Group\GetAll;

use Domain\ReadModel\Member;

final readonly class Query
{
    public function __construct(
        public Member $member,
    ) {
    }
}
