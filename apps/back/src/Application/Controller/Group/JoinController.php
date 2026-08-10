<?php

declare(strict_types=1);

namespace Application\Controller\Group;

use Application\Controller\BaseController;
use Application\Form\Type\JoinGroupType;
use Application\MessageBus\CommandBus;
use Domain\Exception\DomainException;
use Domain\Model\Group as ModelGroup;
use Domain\UseCase\Group\Join\Input;
use Domain\UseCase\Group\Join\Output;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class JoinController extends BaseController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private TranslatorInterface $translator,
    ) {
    }

    #[Route('/group/join/', name: 'group.join')]
    public function __invoke(Request $request): Response
    {
        $form = $this->createForm(JoinGroupType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string */
            $code = $form->get('code')->getData();

            $input = new Input(
                $this->getCurrentMember(),
                $code,
            );

            try {
                /** @var Output */
                $output = $this->commandBus->dispatch($input);
                /** @var ModelGroup */
                $group = $output->group;

                $this->addFlash('success', $this->translator->trans('group.joined', ['%name%' => $group->getName()]));

                return $this->redirectToRoute('group.show', ['id' => $group->getId()]);
            } catch (DomainException $e) {
                $this->addFlash('error', $this->translator->trans($e->getMessage()));
            }
        }

        return $this->redirectToRoute('group.index');
    }
}
