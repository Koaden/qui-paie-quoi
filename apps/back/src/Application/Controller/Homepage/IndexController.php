<?php

declare(strict_types=1);

namespace Application\Controller\Homepage;

use Application\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends BaseController
{
    #[Route('/', name: 'homepage.index')]
    public function __invoke(): Response
    {
        return $this->render('homepage/index.html.twig');
    }
}
