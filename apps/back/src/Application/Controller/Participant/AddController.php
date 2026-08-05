<?php

declare(strict_types=1);

namespace Application\Controller\Participant;

use Application\Controller\BaseController;
use Application\Form\Type\ParticipantType;
use Application\MessageBus\CommandBus;
use Application\MessageBus\QueryBus;
use Domain\Exception\DomainException;
use Domain\Model\Participant;
use Domain\Query\Group\GetSingle\Query;
use Domain\ReadModel\Group;
use Domain\UseCase\Participant\Add\Input;
use Domain\UseCase\Participant\Add\Output;
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

    #[Route('/group/{groupId}/add-participant', name: 'participant.add')]
    public function __invoke(int $groupId, Request $request): Response
    {
        $member = $this->getCurrentMember();

        try {
            /** @var Group */
            $group = $this->queryBus->dispatch(new Query($groupId, $member));
        } catch (DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage()));

            return $this->redirectToRoute('group.index');
        }

        if (!$this->isGranted('ADD_PARTICIPANT', $group)) {
            $this->addFlash('error', $this->translator->trans('action.denied'));

            return $this->redirectToRoute('group.show', ['id' => $group->id]);
        }

        $form = $this->createForm(ParticipantType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $name */
            $name = $form->get('name')->getData();

            $input = new Input(
                $member,
                $name,
                $group,
            );

            try {
                /** @var Output */
                $output = $this->commandBus->dispatch($input);
                /** @var Participant */
                $participant = $output->participant;

                $this->addFlash('success', $this->translator->trans('participant.created', ['%name%' => $participant->getName()]));
            } catch (DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage()));
            }
        }

        $referer = $request->headers->get('referer');

        return $this->redirect($referer ?: $this->generateUrl('group.show', ['id' => $groupId]));
    }
}
