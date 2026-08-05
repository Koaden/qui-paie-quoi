<?php

declare(strict_types=1);

namespace Domain\Exception;

final class MemberPermissionRequired extends DomainException
{
    public function __construct(
    ) {
        parent::__construct(\sprintf('member.permission_required'));
    }
}
