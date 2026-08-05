<?php

declare(strict_types=1);

namespace Application\Controller\Participant;

use Application\Controller\BaseController;
use Application\Form\Type\ParticipantType;
use Application\MessageBus\CommandBus;
use Application\MessageBus\QueryBus;
use Domain\Exception\DomainException;
use Domain\Query\Group\GetSingle\Query as GroupQuery;
use Domain\Query\Participant\GetSingle\Query;
use Domain\ReadModel\Group;
use Domain\ReadModel\Participant;
use Domain\UseCase\Participant\Edit\Input;
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

    #[Route('/group/{groupId}/edit-participant/{id}', name: 'participant.edit')]
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

        if (!$participant->belongsToGroup($group) || !$this->isGranted('EDIT_PARTICIPANT', [$participant, $group])) {
            $this->addFlash('error', $this->translator->trans('action.denied'));

            return $this->redirectToRoute('group.show', ['id' => $group->id]);
        }

        $form = $this->createForm(ParticipantType::class, $participant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Participant */
            $participant = $form->getData();

            $input = Input::fromReadModel($participant, $member);

            try {
                $this->commandBus->dispatch($input);

                $this->addFlash('success', $this->translator->trans('participant.edited', ['%name%' => $participant->name]));
            } catch (DomainException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        $referer = $request->headers->get('referer');

        return $this->redirect($referer ?: $this->generateUrl('group.index'));
    }
}
