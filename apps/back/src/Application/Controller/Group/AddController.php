<?php

declare(strict_types=1);

namespace Application\Controller\Group;

use Application\Controller\BaseController;
use Application\Form\Type\GroupType;
use Application\MessageBus\CommandBus;
use Domain\Exception\DomainException;
use Domain\Model\Group as ModelGroup;
use Domain\UseCase\Group\Add\Input;
use Domain\UseCase\Group\Add\Output;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AddController extends BaseController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private TranslatorInterface $translator,
    ) {
    }

    #[Route('/group/add', name: 'group.add')]
    public function __invoke(Request $request): Response
    {
        $form = $this->createForm(GroupType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string */
            $name = $form->get('name')->getData();
            /** @var ?string */
            $description = $form->get('description')->getData();

            $input = new Input(
                $name,
                $this->getCurrentMember(),
                $description,
            );

            try {
                /** @var Output */
                $output = $this->commandBus->dispatch($input);
                /** @var ModelGroup */
                $group = $output->group;

                $this->addFlash('success', $this->translator->trans('group.created', ['%name%' => $group->getName()]));

                return $this->redirectToRoute('group.show', ['id' => $group->getId()]);
            } catch (DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage()));
            }
        }

        return $this->render('group/add.html.twig', [
            'form' => $form,
        ]);
    }
}
