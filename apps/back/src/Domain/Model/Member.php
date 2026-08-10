<?php

declare(strict_types=1);

namespace Domain\Model;

use Domain\Enum\GroupRole;

class Member
{
    private ?int $id = null;

    /** @var non-empty-string */
    private string $email;

    /** @var array<string> */
    private array $roles = [];

    private string $password;

    private Participant $participant;

    /** @var iterable<GroupMembership> */
    private iterable $memberships;

    /** @param non-empty-string $email */
    public function __construct(
        string $email,
        string $name,
    ) {
        $this->setEmail($email);
        $this->participant = new Participant($name);
        $this->memberships = [];
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

    public function setParticipant(Participant $participant): self
    {
        $this->participant = $participant;

        if ($participant->getMember() !== $this) {
            $participant->setMember($this);
        }

        return $this;
    }

    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    public function getName(): string
    {
        return $this->participant->getName();
    }

    public function setName(string $name): self
    {
        $this->participant->setName($name);

        return $this;
    }

    /** @return iterable<Expense> */
    public function getExpenses(): iterable
    {
        return $this->participant->getExpenses();
    }

    /** @return iterable<Group> */
    public function getGroups(): iterable
    {
        /** @var GroupMembership $membership */
        foreach ($this->memberships as $membership) {
            yield $membership->getGroup();
        }
    }

    public function addGroup(Group $group, GroupRole $role = GroupRole::VIEWER, ?Participant $participant = null): self
    {
        if ($participant) {
            $this->participant->addExpenses($participant->getExpenses());
            $participant->transfertAllExpensePayerRole($this->participant);
            $participant->clearExpenses();
        }

        foreach ($this->memberships as $membership) {
            if ($membership->getGroup() === $group) {
                return $this;
            }
        }

        $memberships = iterator_to_array($this->memberships);

        $memberships[] = new GroupMembership($this, $role, $group);
        $this->memberships = $memberships;

        $this->participant->addGroup($group);

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        $memberships = iterator_to_array($this->memberships);

        foreach ($memberships as $index => $membership) {
            if ($membership->getGroup() === $group) {
                unset($memberships[$index]);
            }
        }
        $this->memberships = $memberships;

        $this->participant->removeGroup($group);

        return $this;
    }

    public function getRoleFromGroup(Group $group): ?GroupRole
    {
        foreach ($this->memberships as $membership) {
            if ($membership->getGroup() === $group) {
                return $membership->getRole();
            }
        }

        return null;
    }

    /** @return iterable<GroupMembership> */
    public function getMemberships(): iterable
    {
        return $this->memberships;
    }

    public function getInitials(): string
    {
        return $this->participant->getInitials();
    }

    /** @return non-empty-string */
    public function getEmail(): string
    {
        return $this->email;
    }

    /** @param non-empty-string $email */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /** @return array<string> */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $hashedPassword): self
    {
        $this->password = $hashedPassword;

        return $this;
    }
}
