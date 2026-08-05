<?php

declare(strict_types=1);

namespace Domain\Collection;

use Domain\Model\Group;

interface GroupCollection
{
    public function add(Group $group): void;
}
