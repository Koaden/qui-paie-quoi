<?php

declare(strict_types=1);

namespace Domain\UseCase\Member\Register;

final readonly class Input
{
    /** @param non-empty-string $email */
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
    }
}
