<?php

declare(strict_types=1);

namespace Domain\Collection;

use Domain\Model\Expense;
use Domain\ReadModel\Member;

interface ExpenseCollection
{
    public function add(Expense $expense): void;

    public function remove(Expense $expense): void;

    public function findOneById(int $id, Member $member): ?Expense;
}
