<?php

declare(strict_types=1);

namespace Domain\UseCase\Expense\Add;

use Domain\ReadModel\Group;
use Domain\ReadModel\Member;
use Domain\ReadModel\Participant;

final readonly class Input
{
    /** @param iterable<Participant> $beneficiaries */
    public function __construct(
        public Member $member,
        public string $title,
        public int $amount,
        public Participant $payer,
        public Group $group,
        public ?\DateTime $date,
        public iterable $beneficiaries,
    ) {
    }
}
