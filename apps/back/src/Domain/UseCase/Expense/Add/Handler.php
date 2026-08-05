<?php

declare(strict_types=1);

namespace Domain\UseCase\Expense\Add;

use Domain\Collection\ExpenseCollection;
use Domain\Collection\MemberCollection;
use Domain\Collection\ParticipantCollection;
use Domain\Exception\GroupDoesntExist;
use Domain\Exception\MemberDoesntExist;
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
        /** @var iterable<Participant> $modelBeneficiaries */
        $modelBeneficiaries = [];

        $beneficiaries = [];
        foreach ($input->beneficiaries as $beneficiarie) {
            if (!($beneficiarie = $this->participantCollection->findOneById($beneficiarie->id))) {
                throw new ParticipantDoesntExist();
            }

            $beneficiaries[] = $beneficiarie;

            /** @var iterable<Participant> $modelBeneficiaries */
            $modelBeneficiaries = $beneficiaries;
        }

        if (!($payer = $this->participantCollection->findOneById($input->payer->id))) {
            throw new ParticipantDoesntExist();
        }

        if (!($group = $this->memberCollection->findOneGroupById($input->group->id, $input->member))) {
            throw new GroupDoesntExist();
        }

        if (!($creator = $this->memberCollection->findOneById($input->member->id))) {
            throw new MemberDoesntExist();
        }

        $expense = new Expense(
            $input->title,
            $input->amount,
            $payer,
            $group,
            $creator,
            $input->date,
            $modelBeneficiaries
        );

        $this->expenseCollection->add($expense);

        return new Output($expense);
    }
}
