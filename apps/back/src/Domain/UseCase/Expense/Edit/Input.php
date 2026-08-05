<?php

declare(strict_types=1);

namespace Domain\UseCase\Expense\Edit;

use Domain\ReadModel\Expense;
use Domain\ReadModel\Group;
use Domain\ReadModel\Member;
use Domain\ReadModel\Participant;

final readonly class Input
{
    /** @param iterable<Participant> $beneficiaries */
    public function __construct(
        public Member $member,
        public int $id,
        public string $title,
        public int $amount,
        public Participant $payer,
        public Group $group,
        public ?\DateTime $date,
        public iterable $beneficiaries,
    ) {
    }

    public static function fromReadModel(Expense $expense, Member $member): self
    {
        return new self(
            $member,
            $expense->id,
            $expense->title,
            $expense->amount,
            $expense->payer,
            $expense->group,
            $expense->date,
            $expense->beneficiaries,
        );
    }
}
