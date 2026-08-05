<?php

declare(strict_types=1);

namespace Domain\Exception;

final class UserNotConnected extends DomainException
{
    public function __construct(
    ) {
        parent::__construct('member.not_connected');
    }
}
