<?php

declare(strict_types=1);

namespace Domain\Query\Group\GetSingle;

use Domain\Collection\MemberCollection;
use Domain\Exception\GroupDoesntExist;
use Domain\Model\Group as GroupModel;
use Domain\ReadModel\Group;

final readonly class Handler
{
    public function __construct(
        private MemberCollection $memberCollection,
    ) {
    }

    public function __invoke(Query $query): Group
    {
        /** @var GroupModel|null */
        $groupModel = $this->memberCollection->findOneGroupById($query->id, $query->member);

        if (!$groupModel) {
            throw new GroupDoesntExist();
        }

        return Group::fromModel($groupModel);
    }
}
