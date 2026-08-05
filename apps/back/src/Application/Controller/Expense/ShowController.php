<?php

declare(strict_types=1);

namespace Application\Controller\Expense;

use Application\Controller\BaseController;
use Application\MessageBus\QueryBus;
use Domain\Exception\DomainException;
use Domain\Query\Expense\GetSingle\Query;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ShowController extends BaseController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private TranslatorInterface $translator,
    ) {
    }

    #[Route('/expense/{id}', name: 'expense.show')]
    public function __invoke(int $id): Response
    {
        try {
            $expense = $this->queryBus->dispatch(new Query($id, $this->getCurrentMember()));
        } catch (DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage()));

            return $this->redirectToRoute('group.index');
        }

        return $this->render('expense/show.html.twig', [
            'expense' => $expense,
        ]);
    }
}
