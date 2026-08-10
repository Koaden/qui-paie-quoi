<?php

declare(strict_types=1);

namespace Application\Controller\Legal;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MentionsLegalesController extends AbstractController
{
    #[Route('/mentions-legales', name: 'legal.mentions')]
    public function __invoke(): Response
    {
        return $this->render('legal/mentions_legales.html.twig');
    }
}
