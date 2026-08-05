<?php

declare(strict_types=1);

namespace Infrastructure\Twig\Components;

use Domain\Model\Participant;
use Domain\ReadModel\Group;
use Infrastructure\Twig\Enum\ParticipantCompressionEnum;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Participants
{
    /**
     * @var iterable<Participant>
     */
    public iterable $participants;
    public ParticipantCompressionEnum $compression = ParticipantCompressionEnum::DEFAULT;
    public ?string $template = null;
    public ?Group $group = null;

    public const MAX_VISIBLE_PARTICIPANTS = 7;
    public const MAX_UNCOMPRESSED_PARTICIPANTS = 5;

    public function getContainerClass(): string
    {
        $class = 'participants';
        $count = count(iterator_to_array($this->participants));

        if (ParticipantCompressionEnum::NEVER === $this->compression) {
            return $class.' participants--no-compressed';
        }

        if (ParticipantCompressionEnum::DEFAULT === $this->compression && $count <= self::MAX_UNCOMPRESSED_PARTICIPANTS) {
            return $class.' participants--mobile-compressed';
        }

        return $class;
    }

    public function getCompressionMode(): string
    {
        $count = count(iterator_to_array($this->participants));

        return match (true) {
            ParticipantCompressionEnum::NEVER === $this->compression => 'none',
            ParticipantCompressionEnum::DEFAULT === $this->compression && $count <= self::MAX_UNCOMPRESSED_PARTICIPANTS => 'mobile',
            default => 'always',
        };
    }

    /**
     * @return iterable<Participant>
     */
    public function getVisibleParticipants(): iterable
    {
        $count = count(iterator_to_array($this->participants));

        if ($count > self::MAX_VISIBLE_PARTICIPANTS && ParticipantCompressionEnum::NEVER !== $this->compression) {
            $sliced = array_slice(iterator_to_array($this->participants), 0, self::MAX_VISIBLE_PARTICIPANTS - 1);

            return $sliced;
        }

        return $this->participants;
    }

    public function getExtraCount(): int
    {
        if (ParticipantCompressionEnum::NEVER === $this->compression) {
            return 0;
        }
        $count = count(iterator_to_array($this->participants));

        return $count > self::MAX_VISIBLE_PARTICIPANTS ? $count - self::MAX_VISIBLE_PARTICIPANTS : 0;
    }

    public function hasModal(): bool
    {
        return null !== $this->template && '' !== $this->template;
    }
}
