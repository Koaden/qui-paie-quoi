<?php

declare(strict_types=1);

namespace Application\Voter;

use Application\Security\User;
use Domain\Enum\GroupRole;
use Domain\Model\Group as ModelGroup;
use Domain\ReadModel\Group;
use Domain\ReadModel\Member;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AddParticipant implements VoterInterface
{
    /** @param array<string> $attributes */
    public function vote(TokenInterface $token, mixed $subject, array $attributes): int
    {
        if (!in_array('ADD_PARTICIPANT', $attributes)) {
            return self::ACCESS_ABSTAIN;
        }

        if (!($subject instanceof Group || $subject instanceof ModelGroup)) {
            return self::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return self::ACCESS_DENIED;
        }

        $member = Member::fromModel($user->getMember());

        if ($subject instanceof ModelGroup) {
            $subject = Group::fromModel($subject);
        }

        $role = $member->getRoleFromGroup($subject);

        if (null === $role || GroupRole::VIEWER === $role) {
            return self::ACCESS_DENIED;
        }

        return self::ACCESS_GRANTED;
    }
}
