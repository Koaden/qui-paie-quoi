<?php

declare(strict_types=1);

namespace Domain\Collection;

use Domain\Model\Group;
use Domain\Model\Member;
use Domain\ReadModel\Member as ReadMember;

interface MemberCollection
{
    public function add(Member $member): void;

    public function findOneByEmail(string $email): ?Member;

    public function findOneById(int $id): ?Member;

    public function findOneGroupById(int $id, ReadMember $member): ?Group;

    /** @return array<Group> */
    public function findAllGroups(ReadMember $member): array;
}
