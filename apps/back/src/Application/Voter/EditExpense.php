<?php

declare(strict_types=1);

namespace Application\Voter;

use Application\Security\User;
use Domain\Enum\GroupRole;
use Domain\Model\Expense as ModelExpense;
use Domain\ReadModel\Expense;
use Domain\ReadModel\Member;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class EditExpense implements VoterInterface
{
    /** @param array<string> $attributes */
    public function vote(TokenInterface $token, mixed $subject, array $attributes): int
    {
        if (!in_array('EDIT_EXPENSE', $attributes)) {
            return self::ACCESS_ABSTAIN;
        }

        if (!($subject instanceof Expense || $subject instanceof ModelExpense)) {
            return self::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return self::ACCESS_DENIED;
        }

        $member = Member::fromModel($user->getMember());

        if ($subject instanceof ModelExpense) {
            $subject = Expense::fromModel($subject);
        }

        if ($member->id !== $subject->creator->id || $member->id !== $subject->group->owner->id) {
            return self::ACCESS_DENIED;
        }

        $role = $member->getRoleFromGroup($subject->group);

        if (null === $role || GroupRole::VIEWER === $role) {
            return self::ACCESS_DENIED;
        }

        return self::ACCESS_GRANTED;
    }
}
