<?php

declare(strict_types=1);

namespace Domain\ReadModel;

use Domain\Model\Participant as ModelParticipant;

final class Participant
{
    public int $id;
    public string $name;
    /** @var iterable<Expense> */
    public iterable $expenses = [];
    /** @var iterable<Group> */
    public iterable $groups = [];
    public ?Member $member = null;

    private function __construct()
    {
    }

    /** @param array<string, mixed> $registry */
    public static function fromModel(
        ModelParticipant $modelParticipant,
        array &$registry = [],
    ): self {
        $hash = spl_object_hash($modelParticipant);

        if (isset($registry[$hash]) && $registry[$hash] instanceof Participant) {
            return $registry[$hash];
        }

        $self = new self();
        $registry[$hash] = $self;

        $self->id = (int) $modelParticipant->getId();
        $self->name = $modelParticipant->getName();

        $expenses = [];

        foreach ($modelParticipant->getExpenses() as $modelExpense) {
            $expenses[] = Expense::fromModel($modelExpense, $registry);
        }

        $self->expenses = $expenses;

        $groups = [];

        foreach ($modelParticipant->getGroups() as $modelGroup) {
            $groups[] = Group::fromModel($modelGroup, $registry);
        }

        $self->groups = $groups;

        if ($modelParticipant->getMember()) {
            $self->member = Member::fromModel(
                $modelParticipant->getMember(),
                $registry
            );
        }

        return $self;
    }

    public function belongsToGroup(Group $group): bool
    {
        foreach ($this->groups as $g) {
            if ($g->id === $group->id) {
                return true;
            }
        }

        return false;
    }

    public function getInitials(): string
    {
        $words = preg_split('/\s+/', trim($this->name)) ?: [];
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
