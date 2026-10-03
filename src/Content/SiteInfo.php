<?php

declare(strict_types=1);

namespace Technum\Content;

use DateTimeImmutable;

final class SiteInfo
{
    private const MONTHS = [
        1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
        'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre',
    ];

    public function __construct(
        public readonly string $email,
        public readonly string $phoneDisplay,
        public readonly string $phoneE164,
        public readonly string $whatsappNumber,
        public readonly string $whatsappMessage,
        public readonly string $githubUrl,
        public readonly string $city,
        public readonly DateTimeImmutable $updatedAt,
    ) {
    }

    public function whatsappUrl(): string
    {
        return 'https://wa.me/' . $this->whatsappNumber . '?text=' . rawurlencode($this->whatsappMessage);
    }

    public function phoneUrl(): string
    {
        return 'tel:' . $this->phoneE164;
    }

    public function emailUrl(): string
    {
        return 'mailto:' . $this->email;
    }

    public function githubLabel(): string
    {
        return (string) preg_replace('#^https://#', '', $this->githubUrl);
    }

    public function updatedAtLabel(): string
    {
        $day = (int) $this->updatedAt->format('j');

        return ($day === 1 ? '1er' : (string) $day)
            . ' ' . self::MONTHS[(int) $this->updatedAt->format('n')]
            . ' ' . $this->updatedAt->format('Y');
    }
}
