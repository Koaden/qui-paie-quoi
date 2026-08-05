<?php

declare(strict_types=1);

namespace Domain\ReadModel;

use Domain\Enum\GroupRole;
use Domain\Model\Member as ModelMember;

final class Member
{
    public int $id;
    public string $email;
    /** @var array<string> */
    public array $roles;
    public string $password;
    public Participant $participant;
    /** @var iterable<GroupMembership> */
    public iterable $memberships;

    private function __construct()
    {
    }

    /** @param array<string, mixed> $registry */
    public static function fromModel(
        ModelMember $modelMember,
        array &$registry = [],
    ): self {
        $hash = spl_object_hash($modelMember);

        if (isset($registry[$hash]) && $registry[$hash] instanceof Member) {
            return $registry[$hash];
        }

        $self = new self();
        $registry[$hash] = $self;

        $self->id = (int) $modelMember->getId();
        $self->email = $modelMember->getEmail();
        $self->roles = $modelMember->getRoles();
        $self->password = $modelMember->getPassword();
        $self->participant = Participant::fromModel(
            $modelMember->getParticipant(),
            $registry
        );

        $memberships = [];

        foreach ($modelMember->getMemberships() as $modelMembership) {
            $memberships[] = GroupMembership::fromModel(
                $modelMembership,
                $registry
            );
        }

        $self->memberships = $memberships;

        return $self;
    }

    public function getName(): string
    {
        return $this->participant->name;
    }

    /** @return iterable<Group> */
    public function getGroups(): iterable
    {
        return $this->participant->groups;
    }

    public function getInitials(): string
    {
        return $this->participant->getInitials();
    }

    public function getRoleFromGroup(Group $group): ?GroupRole
    {
        foreach ($this->memberships as $membership) {
            if ($membership->group->id === $group->id) {
                return $membership->role;
            }
        }

        return null;
    }
}
