<?php

declare(strict_types=1);

namespace Domain\ReadModel;

use Domain\Model\Expense as ModelExpense;

final class Expense
{
    public int $id;
    public string $title;
    public int $amount;
    public ?\DateTime $date;
    public Participant $payer;
    /** @var iterable<Participant> */
    public iterable $beneficiaries;
    public Group $group;
    public Member $creator;

    /** @param array<string, mixed> $registry */
    public static function fromModel(
        ModelExpense $modelExpense,
        array &$registry = [],
    ): self {
        $hash = spl_object_hash($modelExpense);

        if (isset($registry[$hash]) && $registry[$hash] instanceof Expense) {
            return $registry[$hash];
        }

        $self = new self();
        $registry[$hash] = $self;

        $self->id = (int) $modelExpense->getId();
        $self->title = $modelExpense->getTitle();
        $self->amount = $modelExpense->getAmount();
        $self->date = $modelExpense->getDate();

        $self->payer = Participant::fromModel(
            $modelExpense->getPayer(),
            $registry
        );

        $self->creator = Member::fromModel(
            $modelExpense->getCreator(),
            $registry
        );

        $beneficiaries = [];

        foreach ($modelExpense->getBeneficiaries() as $modelParticipant) {
            $beneficiaries[] = Participant::fromModel(
                $modelParticipant,
                $registry
            );
        }

        $self->beneficiaries = $beneficiaries;

        $self->group = Group::fromModel(
            $modelExpense->getGroup(),
            $registry
        );

        return $self;
    }

    public function getParticipantBalance(Participant $participant): int
    {
        $result = 0;

        foreach ($this->beneficiaries as $beneficiarie) {
            if ($beneficiarie->id === $participant->id) {
                $result -= (int) ($this->amount / iterator_count($this->beneficiaries));
                break;
            }
        }
        if ($this->payer->id === $participant->id) {
            $result += $this->amount;
        }

        return $result;
    }
}
