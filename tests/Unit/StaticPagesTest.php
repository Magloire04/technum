<?php

declare(strict_types=1);

namespace Technum\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class StaticPagesTest extends TestCase
{
    public function testServerErrorPageOffersTheServiceAddress(): void
    {
        $page = (string) file_get_contents(__DIR__ . '/../../public/500.html');

        self::assertStringContainsString('mailto:technum.services@bytechnum.com', $page);
        self::assertStringNotContainsString('elisee.atonde', $page);
    }
}
