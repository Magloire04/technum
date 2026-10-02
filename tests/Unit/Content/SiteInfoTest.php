<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Content;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Technum\Content\SiteInfo;

final class SiteInfoTest extends TestCase
{
    private static function site(string $date = '2026-10-02'): SiteInfo
    {
        return new SiteInfo(
            email: 'elisee.atonde@bytechnum.com',
            phoneDisplay: '+229 01 50 61 73 00',
            phoneE164: '+2290150617300',
            whatsappNumber: '2290150617300',
            whatsappMessage: "Bonjour TECHNUM, je souhaite vous parler d'un projet.",
            githubUrl: 'https://github.com/Magloire04',
            city: 'Porto-Novo, Bénin',
            updatedAt: new DateTimeImmutable($date),
        );
    }

    public function testContactLinks(): void
    {
        $site = self::site();

        self::assertSame(
            'https://wa.me/2290150617300?text=Bonjour%20TECHNUM%2C%20je%20souhaite%20vous%20parler%20d%27un%20projet.',
            $site->whatsappUrl(),
        );
        self::assertSame('tel:+2290150617300', $site->phoneUrl());
        self::assertSame('mailto:elisee.atonde@bytechnum.com', $site->emailUrl());
        self::assertSame('github.com/Magloire04', $site->githubLabel());
    }

    public function testUpdateDateIsWrittenInFrench(): void
    {
        self::assertSame('2 octobre 2026', self::site('2026-10-02')->updatedAtLabel());
        self::assertSame('1er août 2026', self::site('2026-08-01')->updatedAtLabel());
    }
}
