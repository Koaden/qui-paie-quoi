<?php

declare(strict_types=1);

namespace Application\Voter;

use Application\Security\User;
use Domain\Enum\GroupRole;
use Domain\Model\Group as ModelGroup;
use Domain\Model\Participant as ModelParticipant;
use Domain\ReadModel\Group;
use Domain\ReadModel\Member;
use Domain\ReadModel\Participant;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class InviteMember implements VoterInterface
{
    /** @param array<string> $attributes */
    public function vote(TokenInterface $token, mixed $subject, array $attributes): int
    {
        if (!in_array('INVITE_MEMBER', $attributes)) {
            return self::ACCESS_ABSTAIN;
        }

        if (!is_array($subject) || 2 !== count($subject)) {
            return self::ACCESS_ABSTAIN;
        }

        [$participant, $group] = $subject;

        if (!($participant instanceof Participant || $participant instanceof ModelParticipant)) {
            return self::ACCESS_ABSTAIN;
        }

        if (!($group instanceof Group || $group instanceof ModelGroup)) {
            return self::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return self::ACCESS_DENIED;
        }

        $member = Member::fromModel($user->getMember());

        if ($participant instanceof ModelParticipant) {
            $participant = Participant::fromModel($participant);
        }

        if ($group instanceof ModelGroup) {
            $group = Group::fromModel($group);
        }

        if (GroupRole::OWNER !== $member->getRoleFromGroup($group)) {
            return self::ACCESS_DENIED;
        }

        if (null !== $participant->member) {
            return self::ACCESS_DENIED;
        }

        return self::ACCESS_GRANTED;
    }
}
