<?php

declare(strict_types=1);

namespace Domain\Model;

class Participant
{
    private ?int $id = null;

    private string $name;

    /** @var iterable<Expense> */
    private iterable $expenses;

    /** @var iterable<Group> */
    private iterable $groups;

    private ?Member $member = null;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->expenses = [];
        $this->groups = [];
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /** @return iterable<Expense> */
    public function getExpenses(): iterable
    {
        return $this->expenses;
    }

    /** @param iterable<Expense> $expenses */
    public function addExpenses(iterable $expenses): self
    {
        // On part des dépenses déjà existantes
        $newExpenses = iterator_to_array($this->expenses);

        foreach ($expenses as $expense) {
            $exists = false;

            foreach ($newExpenses as $e) {
                if ($e === $expense) {
                    $exists = true;
                    break;
                }
            }

            if (!$exists) {
                $newExpenses[] = $expense;
                $expense->addBeneficiary($this);
            }
        }

        $this->expenses = $newExpenses;

        return $this;
    }

    public function transfertAllExpensePayerRole(Participant $participant): self
    {
        foreach ($this->expenses as $expense) {
            $expense->setPayer($participant);
        }

        return $this;
    }

    public function clearExpenses(): self
    {
        foreach ($this->expenses as $expense) {
            $expense->removeBeneficiary($this);
        }

        return $this;
    }

    /** @return iterable<Group> */
    public function getGroups(): iterable
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        foreach ($this->groups as $existing) {
            if ($existing === $group) {
                return $this;
            }
        }

        $groups = iterator_to_array($this->groups);

        $groups[] = $group;
        $this->groups = $groups;

        $group->addParticipant($this);

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        $groups = iterator_to_array($this->groups);

        foreach ($groups as $index => $existing) {
            if ($existing === $group) {
                unset($groups[$index]);
                $group->removeParticipant($this);
            }
        }
        $this->groups = $groups;

        return $this;
    }

    public function setMember(?Member $member): self
    {
        $this->member = $member;

        if ($member && $member->getParticipant() !== $this) {
            $member->setParticipant($this);
        }

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function belongsToGroup(Group $group): bool
    {
        foreach ($this->groups as $g) {
            if ($g === $group) {
                return true;
            }
        }

        return false;
    }

    public function getInitials(): string
    {
        $words = preg_split('/\s+/', trim($this->getName())) ?: [];
        $initials = '';
        foreach ($words as $word) {
            if ('' !== $word) {
                $initials .= mb_substr($word, 0, 1);
            }
            if (mb_strlen($initials) >= 2) {
                break;
            }
        }

        return strtoupper($initials);
    }
}
