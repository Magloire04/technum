<?php

declare(strict_types=1);

namespace Technum\Content;

final class Product
{
    public function __construct(
        public readonly string $slug,
        public readonly string $name,
        public readonly string $url,
        public readonly string $tagline,
        public readonly string $audience,
        public readonly ProductStage $stage,
        public readonly string $done,
        public readonly string $next,
        public readonly string $note,
        public readonly string $icon,
        public readonly ProductImage $image,
    ) {
    }

    public function host(): string
    {
        return (string) parse_url($this->url, PHP_URL_HOST);
    }

    /**
     * Adresse du produit marquée pour repérer, dans ses journaux, les visites venues de bytechnum.com.
     */
    public function trackedUrl(): string
    {
        return rtrim($this->url, '/') . '/?ref=bytechnum';
    }

    public function anchor(): string
    {
        return 'produit-' . $this->slug;
    }

    public function isStable(): bool
    {
        return $this->next === '';
    }
}
