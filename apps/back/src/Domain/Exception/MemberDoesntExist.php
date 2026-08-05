<?php

declare(strict_types=1);

namespace Domain\Exception;

final class MemberDoesntExist extends DomainException
{
    public function __construct(
    ) {
        parent::__construct('member.doesnt_exist');
    }
}
