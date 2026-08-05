<?php

declare(strict_types=1);

namespace Domain\Exception;

final class GroupDoesntExist extends DomainException
{
    public function __construct(
    ) {
        parent::__construct('group.doesnt_exist');
    }
}
