<?php

declare(strict_types=1);

namespace Domain\UseCase\Member\Register;

use Domain\Collection\MemberCollection;
use Domain\Exception\MemberAlreadyExistsException;
use Domain\Model\Member;
use Domain\Security\PasswordHasher;

final readonly class Handler
{
    public function __construct(
        private MemberCollection $memberCollection,
        private PasswordHasher $passwordHasher,
    ) {
    }

    public function __invoke(Input $input): Output
    {
        if ($this->memberCollection->findOneByEmail($input->email)) {
            throw new MemberAlreadyExistsException($input->email);
        }

        $member = new Member(
            $input->email,
            $input->name,
        );

        $member->setPassword($this->passwordHasher->hashPassword($member, $input->password));

        $this->memberCollection->add($member);

        return new Output($member);
    }
}
