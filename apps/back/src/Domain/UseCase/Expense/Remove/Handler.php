<?php

declare(strict_types=1);

namespace Domain\UseCase\Expense\Remove;

use Domain\Collection\ExpenseCollection;
use Domain\Exception\ExpenseDoesntExist;

final readonly class Handler
{
    public function __construct(
        private ExpenseCollection $expenseCollection,
    ) {
    }

    public function __invoke(Input $input): Output
    {
        if (!($expense = $this->expenseCollection->findOneById($input->id, $input->member))) {
            throw new ExpenseDoesntExist();
        }

        $this->expenseCollection->remove($expense);

        return new Output();
    }
}
