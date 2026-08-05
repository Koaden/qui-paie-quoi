<?php

declare(strict_types=1);

namespace Infrastructure\Symfony\Security;

use Application\Security\User;
use Domain\Model\Member;
use Domain\Security\PasswordHasher as SecurityPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class PasswordHasher implements SecurityPasswordHasher
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function hashPassword(Member $member, string $plainPassword): string
    {
        $user = new User($member);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

        return $hashedPassword;
    }

    public function isPasswordValid(Member $member, string $plainPassword): bool
    {
        $user = new User($member);

        return $this->passwordHasher->isPasswordValid($user, $plainPassword);
    }
}
