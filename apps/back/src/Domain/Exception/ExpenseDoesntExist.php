<?php

declare(strict_types=1);

namespace Domain\Exception;

final class ExpenseDoesntExist extends DomainException
{
    public function __construct(
    ) {
        parent::__construct('expense.doesnt_exist');
    }
}
