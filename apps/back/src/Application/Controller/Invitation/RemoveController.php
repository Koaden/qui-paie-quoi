<?php

declare(strict_types=1);

namespace Application\Controller\Invitation;

use Application\Controller\BaseController;
use Application\MessageBus\CommandBus;
use Application\MessageBus\QueryBus;
use Domain\Exception\DomainException;
use Domain\Query\Invitation\GetSingle\Query;
use Domain\UseCase\Invitation\Remove\Input;
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

    #[Route('/invitation/remove/{id}', name: 'invitation.remove')]
    public function remove(int $id): Response
    {
        try {
            /** @var Invitation */
            $invitation = $this->queryBus->dispatch(new Query($id));
        } catch (DomainException $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('group.index');
        }

        $input = new Input($id);

        try {
            $this->commandBus->dispatch($input);
            $this->addFlash('success', $this->translator->trans('invitation.removed', ['%name%' => $invitation->participant->name]));
        } catch (DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('group.show', ['id' => $invitation->group->id]);
    }
}
