<?php

declare(strict_types=1);

namespace Domain\Query\Group\GetAll;

use Domain\Collection\MemberCollection;
use Domain\Model\Group as ModelGroup;
use Domain\ReadModel\Group;

final readonly class Handler
{
    public function __construct(
        private MemberCollection $memberCollection,
    ) {
    }

    /** @return iterable<Group> */
    public function __invoke(Query $query): iterable
    {
        /** @var iterable<ModelGroup> $modelGroups */
        $modelGroups = $this->memberCollection->findAllGroups($query->member);

        /** @var array<Group> $groups */
        $groups = [];

        foreach ($modelGroups as $modelGroup) {
            $groups[] = Group::fromModel($modelGroup);
        }

        return $groups;
    }
}
