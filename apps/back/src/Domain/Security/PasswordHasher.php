<?php

declare(strict_types=1);

namespace Domain\Security;

use Domain\Model\Member;

interface PasswordHasher
{
    public function hashPassword(Member $member, string $plainPassword): string;

    public function isPasswordValid(Member $member, string $plainPassword): bool;
}
