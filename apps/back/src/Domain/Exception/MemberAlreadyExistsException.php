<?php

declare(strict_types=1);

namespace Domain\Exception;

final class MemberAlreadyExistsException extends DomainException
{
    public function __construct(
        string $email,
    ) {
        parent::__construct(\sprintf('The user registered with "%s" already exists.', $email));
    }
}
