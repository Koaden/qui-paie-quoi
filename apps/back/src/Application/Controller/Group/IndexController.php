<?php

declare(strict_types=1);

namespace Application\Controller\Group;

use Application\Controller\BaseController;
use Application\Form\Type\JoinGroupType;
use Application\MessageBus\QueryBus;
use Domain\Exception\DomainException;
use Domain\Query\Group\GetAll\Query;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class IndexController extends BaseController
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private TranslatorInterface $translator,
    ) {
    }

    #[Route('/groups', name: 'group.index')]
    public function __invoke(): Response
    {
        try {
            $groups = $this->queryBus->dispatch(new Query($this->getCurrentMember()));
        } catch (DomainException $e) {
            $this->addFlash('error', $this->translator->trans($e->getMessage()));

            return $this->redirectToRoute('member.login');
        }

        return $this->render('group/index.html.twig', [
            'groups' => $groups,
            'joinForm' => $this->createForm(JoinGroupType::class),
        ]);
    }
}
