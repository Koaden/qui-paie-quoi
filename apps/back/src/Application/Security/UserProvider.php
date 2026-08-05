<?php

declare(strict_types=1);

namespace Application\Security;

use Domain\Collection\MemberCollection;
use Domain\Model\Member;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/** @implements UserProviderInterface<User> */
final class UserProvider implements UserProviderInterface
{
    public function __construct(
        private readonly MemberCollection $members,
    ) {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        /** @var Member|null */
        $member = $this->members->findOneByEmail($identifier);

        if (null === $member) {
            throw new UserNotFoundException();
        }

        return new User($member);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier(
            $user->getUserIdentifier()
        );
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}
