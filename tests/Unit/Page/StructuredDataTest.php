<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Page;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Technum\Content\SiteInfo;
use Technum\Page\StructuredData;

final class StructuredDataTest extends TestCase
{
    public function testOrganizationDescribesTechnum(): void
    {
        $site = new SiteInfo(
            email: 'elisee.atonde@bytechnum.com',
            phoneDisplay: '+229 01 50 61 73 00',
            phoneE164: '+2290150617300',
            whatsappNumber: '2290150617300',
            whatsappMessage: 'Bonjour.',
            githubUrl: 'https://github.com/Magloire04',
            city: 'Porto-Novo, Bénin',
            updatedAt: new DateTimeImmutable('2026-10-02'),
        );

        $json = StructuredData::organization($site);
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($data);
        self::assertSame('Organization', $data['@type']);
        self::assertSame('TECHNUM', $data['name']);
        self::assertSame('+2290150617300', $data['telephone']);
        self::assertSame('https://bytechnum.com/assets/img/logo-technum-carre-512.png', $data['logo']);
        self::assertStringNotContainsString('<', $json);
    }
}
