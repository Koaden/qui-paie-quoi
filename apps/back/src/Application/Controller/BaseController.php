<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Security\User;
use Domain\Exception\UserNotConnected;
use Domain\ReadModel\Member;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    public function getCurrentMember(): Member
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new UserNotConnected();
        }

        return Member::fromModel($user->getMember());
    }
}
