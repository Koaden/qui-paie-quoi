<?php

declare(strict_types=1);

namespace Application\Controller\Member;

use Application\Controller\BaseController;
use Application\Form\Type\ProfileType;
use Application\MessageBus\CommandBus;
use Domain\Exception\DomainException;
use Domain\UseCase\Member\Edit\Input;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class EditController extends BaseController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private TranslatorInterface $translator,
    ) {
    }

    #[Route('/member/edit', name: 'member.edit')]
    public function __invoke(Request $request): Response
    {
        $member = $this->getCurrentMember();

        $form = $this->createForm(ProfileType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string */
            $name = $form->get('name')->getData();
            /** @var non-empty-string */
            $email = $form->get('email')->getData();
            /** @var non-empty-string */
            $password = $form->get('password')->getData();

            $input = new Input($member, $name, $email, $password);

            try {
                $this->commandBus->dispatch($input);

                $this->addFlash('success', $this->translator->trans('participant.edited', ['%name%' => $member->participant->name]));
            } catch (DomainException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        $referer = $request->headers->get('referer');

        return $this->redirect($referer ?: $this->generateUrl('group.index'));
    }
}
