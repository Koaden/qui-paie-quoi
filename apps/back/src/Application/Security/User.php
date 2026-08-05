<?php

declare(strict_types=1);

namespace Application\Security;

use Domain\Model\Member;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private Member $member,
    ) {
    }

    /** @return non-empty-string */
    public function getUserIdentifier(): string
    {
        return $this->member->getEmail();
    }

    /** @return array<string> */
    public function getRoles(): array
    {
        return $this->member->getRoles();
    }

    public function setPassword(string $hashedPassword): void
    {
        $this->member->setPassword($hashedPassword);
    }

    public function getPassword(): ?string
    {
        return $this->member->getPassword();
    }

    public function eraseCredentials(): void
    {
    }

    public function getMember(): Member
    {
        return $this->member;
    }
}
