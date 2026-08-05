<?php

declare(strict_types=1);

namespace Application\Controller\Member;

use Application\Controller\BaseController;
use Application\Form\Type\LoginType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends BaseController
{
    #[Route('/login', name: 'member.login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($error = $authenticationUtils->getLastAuthenticationError()) {
            $this->addFlash('success', $error->getMessage());
        }

        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginType::class);

        return $this->render('member/login.html.twig', [
            'last_username' => $lastUsername,
            'form' => $form,
        ]);
    }
}
