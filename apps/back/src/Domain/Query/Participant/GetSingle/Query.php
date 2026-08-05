<?php

declare(strict_types=1);

namespace Domain\Query\Participant\GetSingle;

final readonly class Query
{
    public function __construct(
        public int $id,
    ) {
    }
}
