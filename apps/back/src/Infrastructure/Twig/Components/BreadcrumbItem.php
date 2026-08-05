<?php

declare(strict_types=1);

namespace Infrastructure\Twig\Components;

final readonly class BreadcrumbItem
{
    public function __construct(
        public string $title,
        public string $url = '',
        public bool $isBack = false,
        public bool $isCurrent = false,
    ) {
    }
}
