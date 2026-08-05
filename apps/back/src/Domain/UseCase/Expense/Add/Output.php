<?php

declare(strict_types=1);

namespace Domain\UseCase\Expense\Add;

use Domain\Model\Expense;

final readonly class Output
{
    public function __construct(
        public Expense $expense,
    ) {
    }
}
