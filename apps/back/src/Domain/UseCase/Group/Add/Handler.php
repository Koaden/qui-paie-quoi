<?php

declare(strict_types=1);

namespace Domain\UseCase\Group\Add;

use Domain\Collection\GroupCollection;
use Domain\Collection\MemberCollection;
use Domain\Collection\MembershipCollection;
use Domain\Enum\GroupRole;
use Domain\Exception\MemberDoesntExist;
use Domain\Model\Group;
use Domain\Model\GroupMembership;

final readonly class Handler
{
    public function __construct(
        private GroupCollection $groupCollection,
        private MemberCollection $memberCollection,
        private MembershipCollection $membershipCollection,
    ) {
    }

    public function __invoke(Input $input): Output
    {
        if (!($owner = $this->memberCollection->findOneById($input->owner->id))) {
            throw new MemberDoesntExist();
        }

        $group = new Group(
            $input->name,
            $owner,
            $input->description,
        );

        $this->groupCollection->add($group);
        $this->membershipCollection->add(new GroupMembership($owner, GroupRole::OWNER, $group));

        return new Output($group);
    }
}
