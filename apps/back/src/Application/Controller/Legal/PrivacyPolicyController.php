<?php

declare(strict_types=1);

namespace Application\Controller\Legal;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PrivacyPolicyController extends AbstractController
{
    #[Route('/politique-de-confidentialite', name: 'legal.privacy')]
    public function __invoke(): Response
    {
        return $this->render('legal/privacy_policy.html.twig');
    }
}
