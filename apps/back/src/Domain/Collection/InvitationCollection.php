<?php

declare(strict_types=1);

namespace Domain\Collection;

use Domain\Model\Invitation;

interface InvitationCollection
{
    public function add(Invitation $invitation): void;

    public function remove(Invitation $invitation): void;

    public function findOneById(int $id): ?Invitation;

    public function findOneByCode(string $code): ?Invitation;
}
