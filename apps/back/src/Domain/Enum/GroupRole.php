<?php

declare(strict_types=1);

namespace Domain\Enum;

enum GroupRole: string
{
    case OWNER = 'OWNER';
    case EDITOR = 'EDITOR';
    case VIEWER = 'VIEWER';
}
