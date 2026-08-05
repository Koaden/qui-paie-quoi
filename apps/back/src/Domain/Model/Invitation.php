<?php

declare(strict_types=1);

namespace Domain\Model;

class Invitation
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    private ?int $id = null;

    private string $code;

    private Group $group;

    private Participant $participant;

    public function __construct(Group $group, Participant $participant)
    {
        $this->group = $group;
        $this->participant = $participant;
        $this->code = $this->generateCode();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function generateCode(): string
    {
        $groupPart = $this->shortCode((string) $this->group->getId());
        $participantPart = $this->shortCode((string) $this->participant->getId());
        $uuidPart = $this->shortCode(uniqid());

        $this->code = sprintf('%s-%s-%s', $groupPart, $participantPart, $uuidPart);

        return $this->code;
    }

    private function shortCode(string $data, int $length = 4): string
    {
        $hash = hash('xxh3', $data, true);
        $num = unpack('J', substr($hash, 0, 8))[1];

        $base = strlen(self::ALPHABET);
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= self::ALPHABET[$num % $base];
            $num = intdiv($num, $base);
        }

        return $code;
    }
}
