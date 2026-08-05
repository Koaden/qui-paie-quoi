<?php

declare(strict_types=1);

namespace Domain\ReadModel;

readonly class Debt
{
    public function __construct(
        public Participant $debtor,
        public Participant $creditor,
        public int $amount,
    ) {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('The debt should be greater than 0.');
        }
    }

    public function getParticipantAmount(Participant $participant): int
    {
        return match (true) {
            $this->debtor === $participant => -$this->amount,
            $this->creditor === $participant => $this->amount,
            default => 0,
        };
    }
}
