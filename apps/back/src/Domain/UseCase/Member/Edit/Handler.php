<?php

declare(strict_types=1);

namespace Domain\UseCase\Member\Edit;

use Domain\Collection\MemberCollection;
use Domain\Exception\InvalidPassword;
use Domain\Exception\MemberDoesntExist;
use Domain\Security\PasswordHasher;

final readonly class Handler
{
    public function __construct(
        private readonly MemberCollection $memberCollection,
        private PasswordHasher $passwordHasher,
    ) {
    }

    public function __invoke(Input $input): Output
    {
        if (!($member = $this->memberCollection->findOneById($input->member->id))) {
            throw new MemberDoesntExist();
        }

        if (!$this->passwordHasher->isPasswordValid($member, $input->plainPassword)) {
            throw new InvalidPassword();
        }

        $member->setName($input->name);
        $member->setEmail($input->email);

        $this->memberCollection->add($member);

        return new Output($member);
    }
}
