<?php

declare(strict_types=1);

namespace Technum\Content;

final class ServiceExample
{
    public function __construct(
        public readonly string $label,
        public readonly string $href,
    ) {
    }

    public function hasLink(): bool
    {
        return $this->href !== '';
    }
}
