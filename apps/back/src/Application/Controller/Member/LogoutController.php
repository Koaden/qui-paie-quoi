<?php

declare(strict_types=1);

namespace Application\Controller\Member;

use Application\Controller\BaseController;
use Symfony\Component\Routing\Attribute\Route;

class LogoutController extends BaseController
{
    #[Route('/logout', name: 'member.logout')]
    public function logout(): void
    {
    }
}
