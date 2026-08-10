<?php

declare(strict_types=1);

namespace Domain\Exception;

final class MemberAlreadyInThisGroup extends DomainException
{
    public function __construct(
    ) {
        parent::__construct('member.already_in_group');
    }
}
