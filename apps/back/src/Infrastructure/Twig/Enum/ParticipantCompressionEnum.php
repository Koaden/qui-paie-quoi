<?php

declare(strict_types=1);

namespace Infrastructure\Twig\Enum;

enum ParticipantCompressionEnum: string
{
    case DEFAULT = 'default';
    case NEVER = 'never';
    case ALWAYS = 'always';
}
