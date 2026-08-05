<?php

declare(strict_types=1);

namespace Domain\UseCase\Invitation\Remove;

use Domain\Collection\InvitationCollection;
use Domain\Exception\InvitationDoesntExist;

final readonly class Handler
{
    public function __construct(
        private InvitationCollection $invitationCollection,
    ) {
    }

    public function __invoke(Input $input): Output
    {
        if (!($invitation = $this->invitationCollection->findOneById($input->id))) {
            throw new InvitationDoesntExist();
        }

        $this->invitationCollection->remove($invitation);

        return new Output();
    }
}
