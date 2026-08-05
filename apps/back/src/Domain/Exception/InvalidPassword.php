<?php

declare(strict_types=1);

namespace Domain\Exception;

final class InvalidPassword extends DomainException
{
    public function __construct(
    ) {
        parent::__construct(\sprintf('error.invalid_password'));
    }
}
