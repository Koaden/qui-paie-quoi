<?php

declare(strict_types=1);

namespace Domain\Query\Invitation\GetSingle;

use Domain\Collection\InvitationCollection;
use Domain\Exception\InvitationDoesntExist;
use Domain\Model\Invitation as InvitationModel;
use Domain\ReadModel\Invitation;

final readonly class Handler
{
    public function __construct(
        private InvitationCollection $invitationCollection,
    ) {
    }

    public function __invoke(Query $query): Invitation
    {
        /** @var InvitationModel|null */
        $invitationModel = $this->invitationCollection->findOneById($query->id);

        if (!$invitationModel) {
            throw new InvitationDoesntExist();
        }

        return Invitation::fromModel($invitationModel);
    }
}
