<?php

declare(strict_types=1);

namespace Application\Controller\Expense;

use Application\Controller\BaseController;
use Application\Form\Type\ExpenseType;
use Application\Form\Type\ParticipantType;
use Application\MessageBus\CommandBus;
use Application\MessageBus\QueryBus;
use Domain\Exception\DomainException;
use Domain\Model\Expense as ModelExpense;
use Domain\Query\Group\GetSingle\Query;
use Domain\ReadModel\Group;
use Domain\ReadModel\Participant;
use Domain\UseCase\Expense\Add\Input;
use Domain\UseCase\Expense\Add\Output;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AddController extends BaseController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
        private TranslatorInterface $translator,
    ) {
    }

    #[Route('/group/{groupId}/add-expense', name: 'expense.add')]
    public function __invoke(int $groupId, Request $request): Response
    {
        try {
            /** @var Group */
            $group = $this->queryBus->dispatch(new Query($groupId, $this->getCurrentMember()));
        } catch (DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage()));

            return $this->redirectToRoute('group.index');
        }

        if (!$this->isGranted('ADD_EXPENSE', $group)) {
            $this->addFlash('error', $this->translator->trans('action.denied'));

            return $this->redirectToRoute('group.show', ['id' => $group->id]);
        }

        $form = $this->createForm(ExpenseType::class, null, ['participants' => $group->participants]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $title */
            $title = $form->get('title')->getData();

            /** @var int $amount */
            $amount = $form->get('amount')->getData();

            /** @var Participant $payer */
            $payer = $form->get('payer')->getData();

            /** @var \DateTime|null $date */
            $date = $form->get('date')->getData();

            /** @var iterable<Participant> $beneficiaries */
            $beneficiaries = $form->get('beneficiaries')->getData();

            $input = new Input(
                $this->getCurrentMember(),
                $title,
                $amount,
                $payer,
                $group,
                $date,
                $beneficiaries
            );

            try {
                /** @var Output */
                $output = $this->commandBus->dispatch($input);
                /** @var ModelExpense */
                $expense = $output->expense;

                $this->addFlash('success', $this->translator->trans('expense.created', ['%title%' => $expense->getTitle()]));

                return $this->redirectToRoute('expense.show', ['id' => $expense->getId()]);
            } catch (DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage()));
            }
        }

        return $this->render('expense/add.html.twig', [
            'form' => $form,
            'group' => $group,
            'participantForm' => $this->createForm(ParticipantType::class),
            'buttonText' => $this->translator->trans('action.create'),
        ]);
    }
}
