<?php

declare(strict_types=1);

namespace Domain\Collection;

use Domain\Model\GroupMembership;

interface MembershipCollection
{
    public function add(GroupMembership $membership): void;

    public function findOneById(int $id): ?GroupMembership;
}
