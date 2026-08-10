<?php

declare(strict_types=1);

namespace Domain\ReadModel;

use Domain\Model\Invitation as ModelInvitation;

final class Invitation
{
    public ?int $id = null;
    public string $code;
    public Group $group;
    public Participant $participant;

    /** @param array<string, mixed> $registry */
    public static function fromModel(
        ModelInvitation $modelInvitation,
        array &$registry = [],
    ): self {
        $hash = spl_object_hash($modelInvitation);

        if (isset($registry[$hash]) && $registry[$hash] instanceof Invitation) {
            return $registry[$hash];
        }

        $self = new self();
        $registry[$hash] = $self;

        $self->id = (int) $modelInvitation->getId();
        $self->code = $modelInvitation->getCode();
        $self->group = Group::fromModel(
            $modelInvitation->getGroup(),
            $registry
        );
        $self->participant = Participant::fromModel(
            $modelInvitation->getParticipant(),
            $registry
        );

        return $self;
    }
}
