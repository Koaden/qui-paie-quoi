<?php

declare(strict_types=1);

namespace Domain\UseCase\Expense\Edit;

use Domain\Collection\ExpenseCollection;
use Domain\Collection\MemberCollection;
use Domain\Collection\ParticipantCollection;
use Domain\Exception\ExpenseDoesntExist;
use Domain\Exception\GroupDoesntExist;
use Domain\Exception\ParticipantDoesntExist;
use Domain\Model\Expense;
use Domain\Model\Participant;

final readonly class Handler
{
    public function __construct(
        private ExpenseCollection $expenseCollection,
        private ParticipantCollection $participantCollection,
        private MemberCollection $memberCollection,
    ) {
    }

    public function __invoke(Input $input): Output
    {
        /** @var Expense|null */
        $expense = $this->expenseCollection->findOneById($input->id, $input->member);

        if (!$expense) {
            throw new ExpenseDoesntExist();
        }

        /** @var iterable<Participant> $modelBeneficiaries */
        $modelBeneficiaries = [];

        $beneficiaries = [];
        foreach ($input->beneficiaries as $beneficiarie) {
            if (!($beneficiarie = $this->participantCollection->findOneById($beneficiarie->id))) {
                throw new ParticipantDoesntExist();
            }

            $beneficiaries[] = $beneficiarie;

            $modelBeneficiaries = $beneficiaries;
        }

        if (!($payer = $this->participantCollection->findOneById($input->payer->id))) {
            throw new ParticipantDoesntExist();
        }

        if (!($group = $this->memberCollection->findOneGroupById($input->group->id, $input->member))) {
            throw new GroupDoesntExist();
        }

        $expense->setTitle($input->title);
        $expense->setAmount($input->amount);
        $expense->setPayer($payer);
        $expense->setGroup($group);
        $expense->setDate($input->date);
        $expense->setBeneficiaries($modelBeneficiaries);

        $this->expenseCollection->add($expense);

        return new Output($expense);
    }
}
