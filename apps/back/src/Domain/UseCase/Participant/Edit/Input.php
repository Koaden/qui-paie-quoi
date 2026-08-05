<?php

declare(strict_types=1);

namespace Domain\UseCase\Participant\Edit;

use Domain\ReadModel\Member;
use Domain\ReadModel\Participant;

final readonly class Input
{
    public function __construct(
        public Member $member,
        public int $id,
        public string $name,
    ) {
    }

    public static function fromReadModel(Participant $participant, Member $member): self
    {
        return new self(
            $member,
            $participant->id,
            $participant->name,
        );
    }
}
