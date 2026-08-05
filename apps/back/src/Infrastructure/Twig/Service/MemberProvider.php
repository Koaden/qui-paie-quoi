<?php

declare(strict_types=1);

namespace Infrastructure\Twig\Service;

use Application\Form\Type\ProfileType;
use Application\Security\User;
use Domain\ReadModel\Member;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;

class MemberProvider
{
    public function __construct(
        private Security $security,
        private FormFactoryInterface $formFactory,
    ) {
    }

    public function getCurrent(): ?Member
    {
        $user = $this->security->getUser();
        if (!$user || !$user instanceof User) {
            return null;
        }

        return Member::fromModel($user->getMember());
    }

    public function getForm(): ?FormView
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return null;
        }

        return $this->formFactory
            ->create(ProfileType::class, $user->getMember())
            ->createView();
    }
}
