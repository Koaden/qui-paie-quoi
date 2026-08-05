<?php

declare(strict_types=1);

namespace Domain\Model;

use Domain\Enum\GroupRole;

class GroupMembership
{
    private ?int $id = null;
    private Member $member;
    private GroupRole $role;
    private Group $group;

    public function __construct(Member $member, GroupRole $role, Group $group)
    {
        $this->member = $member;
        $this->role = $role;
        $this->group = $group;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getRole(): GroupRole
    {
        return $this->role;
    }

    public function setRole(GroupRole $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getMember(): Member
    {
        return $this->member;
    }

    public function setMember(Member $member): self
    {
        $this->member = $member;

        return $this;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): self
    {
        $this->group = $group;

        return $this;
    }
}
