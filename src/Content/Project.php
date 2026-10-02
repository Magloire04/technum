<?php

declare(strict_types=1);

namespace Technum\Content;

final class Project
{
    public function __construct(
        public readonly string $slug,
        public readonly string $name,
        public readonly string $description,
        public readonly string $nature,
        public readonly string $linkUrl,
        public readonly string $linkLabel,
    ) {
    }

    public function anchor(): string
    {
        return 'realisation-' . $this->slug;
    }

    public function hasLink(): bool
    {
        return $this->linkUrl !== '';
    }
}
