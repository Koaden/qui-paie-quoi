<?php

declare(strict_types=1);

namespace Application\Controller\Member;

use Application\Controller\BaseController;
use Application\Form\Type\MemberType;
use Application\MessageBus\CommandBus;
use Application\Security\User;
use Domain\Exception\DomainException;
use Domain\Model\Member as ModelMember;
use Domain\UseCase\Member\Register\Input;
use Domain\UseCase\Member\Register\Output;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegisterController extends BaseController
{
    public function __construct(
        private TranslatorInterface $translator,
        private readonly CommandBus $commandBus,
    ) {
    }

    #[Route('/register', name: 'member.register')]
    public function register(Request $request, Security $security): Response
    {
        $form = $this->createForm(MemberType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string */
            $name = $form->get('name')->getData();
            /** @var non-empty-string */
            $email = $form->get('email')->getData();
            /** @var non-empty-string */
            $password = $form->get('password')->getData();

            $input = new Input($name, $email, $password);

            try {
                /** @var Output */
                $output = $this->commandBus->dispatch($input);
                /** @var ModelMember */
                $member = $output->member;

                $this->addFlash('success', $this->translator->trans('member.created', ['%name%' => $name]));

                return $security->login(new User($member)) ?: $this->redirectToRoute('group.index');
            } catch (DomainException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('member/register.html.twig', [
            'form' => $form,
        ]);
    }
}
