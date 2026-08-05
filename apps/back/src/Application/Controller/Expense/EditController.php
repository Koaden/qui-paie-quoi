<?php

declare(strict_types=1);

namespace Application\Controller\Expense;

use Application\Controller\BaseController;
use Application\Form\Type\ExpenseType;
use Application\Form\Type\ParticipantType;
use Application\MessageBus\CommandBus;
use Application\MessageBus\QueryBus;
use Domain\Exception\DomainException;
use Domain\Query\Expense\GetSingle\Query;
use Domain\ReadModel\Expense;
use Domain\UseCase\Expense\Edit\Input;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class EditController extends BaseController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
        private TranslatorInterface $translator,
    ) {
    }

    #[Route('/expense/edit/{id}', name: 'expense.edit')]
    public function __invoke(Request $request, int $id): Response
    {
        $member = $this->getCurrentMember();

        try {
            /** @var Expense */
            $expense = $this->queryBus->dispatch(new Query($id, $member));
        } catch (DomainException $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('group.index');
        }

        if (!$this->isGranted('EDIT_EXPENSE', $expense)) {
            $this->addFlash('error', $this->translator->trans('action.denied'));

            return $this->redirectToRoute('expense.show', ['id' => $expense->id]);
        }

        $form = $this->createForm(ExpenseType::class, $expense, ['participants' => $expense->group->participants]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Expense */
            $expense = $form->getData();

            $input = Input::fromReadModel($expense, $member);

            try {
                $this->commandBus->dispatch($input);

                $this->addFlash('success', $this->translator->trans('expense.edited', ['%title%' => $expense->title]));

                return $this->redirectToRoute('expense.show', ['id' => $expense->id]);
            } catch (DomainException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('expense/add.html.twig', [
            'form' => $form,
            'expense' => $expense,
            'group' => $expense->group,
            'participantForm' => $this->createForm(ParticipantType::class),
            'buttonText' => $this->translator->trans('action.edit'),
        ]);
    }
}
