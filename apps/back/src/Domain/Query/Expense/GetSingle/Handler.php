<?php

declare(strict_types=1);

namespace Domain\Query\Expense\GetSingle;

use Domain\Collection\ExpenseCollection;
use Domain\Exception\ExpenseDoesntExist;
use Domain\Model\Expense as ExpenseModel;
use Domain\ReadModel\Expense;

final readonly class Handler
{
    public function __construct(
        private ExpenseCollection $expenseCollection,
    ) {
    }

    public function __invoke(Query $query): Expense
    {
        /** @var ExpenseModel|null */
        $expenseModel = $this->expenseCollection->findOneById($query->id, $query->member);

        if (!$expenseModel) {
            throw new ExpenseDoesntExist();
        }

        return Expense::fromModel($expenseModel);
    }
}
