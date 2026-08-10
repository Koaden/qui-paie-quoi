<?php

declare(strict_types=1);

namespace Domain\Exception;

final class InvitationDoesntExist extends DomainException
{
    public function __construct(
    ) {
        parent::__construct('invitation.doesnt_exist');
    }
}
