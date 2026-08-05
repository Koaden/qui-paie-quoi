<?php

declare(strict_types=1);

namespace Domain\Exception;

final class ParticipantDoesntExist extends DomainException
{
    public function __construct(
    ) {
        parent::__construct('participant.doesnt_exist');
    }
}
