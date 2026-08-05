<?php

declare(strict_types=1);

namespace Domain\UseCase\Invitation\Add;

use Domain\ReadModel\Group;
use Domain\ReadModel\Member;
use Domain\ReadModel\Participant;

final readonly class Input
{
    public function __construct(
        public Member $member,
        public Group $group,
        public Participant $participant,
    ) {
    }
}
