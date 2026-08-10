<?php

declare(strict_types=1);

namespace Application\Controller\Invitation;

use Application\Controller\BaseController;
use Application\MessageBus\CommandBus;
use Application\MessageBus\QueryBus;
use Domain\Exception\DomainException;
use Domain\Model\Invitation;
use Domain\Query\Group\GetSingle\Query as GroupQuery;
use Domain\Query\Participant\GetSingle\Query;
use Domain\ReadModel\Group;
use Domain\ReadModel\Participant;
use Domain\UseCase\Invitation\Add\Input;
use Domain\UseCase\Invitation\Add\Output;
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

    #[Route('/group/{groupId}/participant/{id}/add-invitation', name: 'invitation.add')]
    public function __invoke(Request $request, int $groupId, int $id): Response
    {
        $member = $this->getCurrentMember();

        try {
            /** @var Group */
            $group = $this->queryBus->dispatch(new GroupQuery($groupId, $member));
        } catch (DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage()));

            return $this->redirectToRoute('group.index');
        }

        try {
            /** @var Participant */
            $participant = $this->queryBus->dispatch(new Query($id));
        } catch (DomainException $e) {
            $this->addFlash('danger', $e->getMessage());

            return $this->redirectToRoute('group.show', ['id' => $group->id]);
        }

        if (!$participant->belongsToGroup($group) || !$this->isGranted('INVITE_MEMBER', [$participant, $group])) {
            $this->addFlash('error', $this->translator->trans('action.denied'));

            return $this->redirectToRoute('group.show', ['id' => $group->id]);
        }

        $input = new Input(
            $this->getCurrentMember(),
            $group,
            $participant,
        );

        try {
            /** @var Output */
            $output = $this->commandBus->dispatch($input);
            /** @var Invitation */
            $invitation = $output->invitation;

            $this->addFlash('success', $this->translator->trans('invitation.created', ['%name%' => $participant->name, '%code%' => $invitation->getCode()]));
        } catch (DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage()));
        }

        return $this->redirectToRoute('group.show', ['id' => $group->id]);
    }
}
