<?php

declare(strict_types=1);

namespace Application\Controller\Expense;

use Application\Controller\BaseController;
use Application\MessageBus\CommandBus;
use Application\MessageBus\QueryBus;
use Domain\Exception\DomainException;
use Domain\Query\Expense\GetSingle\Query;
use Domain\ReadModel\Expense;
use Domain\ReadModel\Member;
use Domain\UseCase\Expense\Remove\Input;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RemoveController extends BaseController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
        private TranslatorInterface $translator,
    ) {
    }

    #[Route('/expense/remove/{id}', name: 'expense.remove')]
    public function remove(int $id): Response
    {
        /** @var Member $member */
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

        $input = new Input($member, $id);

        try {
            $this->commandBus->dispatch($input);
            $this->addFlash('success', $this->translator->trans('expense.removed', ['%title%' => $expense->title]));
        } catch (DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('group.show', ['id' => $expense->group->id]);
    }
}
