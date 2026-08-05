<?php

declare(strict_types=1);

namespace Infrastructure\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Breadcrumb
{
    /**
     * @var BreadcrumbItem[]
     */
    public array $items;
}
