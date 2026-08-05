<?php

declare(strict_types=1);

namespace Domain\ReadModel;

use Domain\Enum\GroupRole;
use Domain\Model\GroupMembership as ModelMembership;

final class GroupMembership
{
    public ?int $id = null;
    public Member $member;
    public GroupRole $role;
    public Group $group;

    /** @param array<string, mixed> $registry */
    public static function fromModel(
        ModelMembership $modelMembership,
        array &$registry = [],
    ): self {
        $hash = spl_object_hash($modelMembership);

        if (isset($registry[$hash]) && $registry[$hash] instanceof GroupMembership) {
            return $registry[$hash];
        }

        $self = new self();
        $registry[$hash] = $self;

        $self->id = (int) $modelMembership->getId();
        $self->role = $modelMembership->getRole();
        $self->member = Member::fromModel(
            $modelMembership->getMember(),
            $registry
        );
        $self->group = Group::fromModel(
            $modelMembership->getGroup(),
            $registry
        );

        return $self;
    }
}
