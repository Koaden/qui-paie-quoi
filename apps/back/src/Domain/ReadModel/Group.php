<?php

declare(strict_types=1);

namespace Domain\ReadModel;

use Domain\Model\Group as ModelGroup;

final class Group
{
    private ModelGroup $modelGroup;

    public int $id;
    public string $name;
    public ?string $description;
    public Member $owner;
    /** @var iterable<Participant> */
    public iterable $participants = [];
    /** @var iterable<Expense> */
    public iterable $expenses = [];
    /** @var iterable<Invitation> */
    public iterable $invitations = [];

    private function __construct()
    {
    }

    /** @param array<string, mixed> $registry */
    public static function fromModel(
        ModelGroup $modelGroup,
        array &$registry = [],
    ): self {
        $hash = spl_object_hash($modelGroup);

        if (isset($registry[$hash]) && $registry[$hash] instanceof Group) {
            return $registry[$hash];
        }

        $self = new self();
        $registry[$hash] = $self;

        $self->modelGroup = $modelGroup;

        $self->id = (int) $modelGroup->getId();
        $self->name = $modelGroup->getName();
        $self->description = $modelGroup->getDescription();

        $self->owner = Member::fromModel(
            $modelGroup->getOwner(),
            $registry
        );

        $participants = [];

        foreach ($modelGroup->getParticipants() as $modelParticipant) {
            $participants[] = Participant::fromModel(
                $modelParticipant,
                $registry
            );
        }

        $self->participants = $participants;

        $expenses = [];

        foreach ($modelGroup->getExpenses() as $modelExpense) {
            $expenses[] = Expense::fromModel(
                $modelExpense,
                $registry
            );
        }

        $self->expenses = $expenses;

        $invitations = [];

        foreach ($modelGroup->getInvitations() as $modelInvitation) {
            $invitations[] = Invitation::fromModel(
                $modelInvitation,
                $registry
            );
        }

        $self->invitations = $invitations;

        return $self;
    }

    public function getInvitationFromParticipant(Participant $participant): ?Invitation
    {
        foreach ($this->invitations as $invitation) {
            if ($invitation->participant === $participant) {
                return $invitation;
            }
        }

        return null;
    }

    public function getTotal(): float
    {
        return $this->modelGroup->getTotal();
    }

    public function getNumberOfExpenses(): int
    {
        return $this->modelGroup->getNumberOfExpenses();
    }

    /**
     * @return array<Debt>
     */
    public function getDebts(): array
    {
        /** @var array<Debt> */
        $debts = [];
        /** @var array<int> */
        $balance = [];
        /** @var array<Participant> */
        $creditors = [];
        /** @var array<Participant> */
        $debtors = [];

        foreach ($this->participants as $participant) {
            $balance[$participant->id] = 0;
        }
        foreach ($this->expenses as $expense) {
            foreach ($this->participants as $participant) {
                $balance[$participant->id] += $expense->getParticipantBalance($participant);
            }
        }
        foreach ($this->participants as $participant) {
            if ($balance[$participant->id] > 0) {
                array_push($creditors, $participant);
            } elseif ($balance[$participant->id] < 0) {
                array_push($debtors, $participant);
            }
        }
        foreach ($debtors as $debtor) {
            foreach ($creditors as $creditor) {
                $debt = min(-$balance[$debtor->id], $balance[$creditor->id]);
                if ($debt > 0) {
                    array_push($debts, new Debt($debtor, $creditor, $debt));
                    $balance[$debtor->id] += $debt;
                    $balance[$creditor->id] -= $debt;
                }
            }
        }

        return $debts;
    }
}
