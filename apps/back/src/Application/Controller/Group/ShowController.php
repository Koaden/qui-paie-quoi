<?php

declare(strict_types=1);

namespace Application\Controller\Group;

use Application\Controller\BaseController;
use Application\Form\Type\ParticipantType;
use Application\MessageBus\QueryBus;
use Domain\Exception\DomainException;
use Domain\Query\Group\GetSingle\Query;
use Domain\ReadModel\Group;
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

    #[Route('/group/{id}', name: 'group.show')]
    public function __invoke(int $id): Response
    {
        try {
            /** @var Group */
            $group = $this->queryBus->dispatch(new Query($id, $this->getCurrentMember()));
        } catch (DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage()));

            return $this->redirectToRoute('group.index');
        }

        $forms = [];
        foreach ($group->participants as $participant) {
            $forms[$participant->id] = $this->createForm(ParticipantType::class, $participant)->createView();
        }

        return $this->render('group/show.html.twig', [
            'group' => $group,
            'participantForm' => $this->createForm(ParticipantType::class),
            'participantEditForms' => $forms,
        ]);
    }
}
