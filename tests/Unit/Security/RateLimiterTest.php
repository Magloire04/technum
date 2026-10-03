<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Security;

use PHPUnit\Framework\TestCase;
use Technum\Security\RateLimiter;
use Technum\Tests\Support\TempDirectory;

final class RateLimiterTest extends TestCase
{
    private const NOW = 1_790_000_000;

    private string $directory;

    private RateLimiter $limiter;

    protected function setUp(): void
    {
        $this->directory = TempDirectory::create('technum-rate');
        $this->limiter = new RateLimiter($this->directory, str_repeat('r', 40));
    }

    protected function tearDown(): void
    {
        TempDirectory::remove($this->directory);
    }

    public function testAllowsFiveHitsPerHour(): void
    {
        for ($i = 0; $i < 4; ++$i) {
            $this->limiter->hit('203.0.113.10', self::NOW + $i);
        }
        self::assertFalse($this->limiter->isLimited('203.0.113.10', self::NOW + 10));

        $this->limiter->hit('203.0.113.10', self::NOW + 11);

        self::assertTrue($this->limiter->isLimited('203.0.113.10', self::NOW + 12));
    }

    public function testHitsExpireAfterOneHour(): void
    {
        for ($i = 0; $i < 5; ++$i) {
            $this->limiter->hit('203.0.113.10', self::NOW);
        }

        self::assertTrue($this->limiter->isLimited('203.0.113.10', self::NOW + 3599));
        self::assertFalse($this->limiter->isLimited('203.0.113.10', self::NOW + 3600));
    }

    public function testLimitIsKeptPerClient(): void
    {
        for ($i = 0; $i < 5; ++$i) {
            $this->limiter->hit('203.0.113.10', self::NOW);
        }

        self::assertFalse($this->limiter->isLimited('198.51.100.20', self::NOW));
    }

    public function testStoredFilesNeverContainTheAddress(): void
    {
        $this->limiter->hit('203.0.113.10', self::NOW);
        $files = glob($this->directory . '/*') ?: [];

        self::assertCount(1, $files);
        self::assertStringNotContainsString('203.0.113.10', basename($files[0]));
        self::assertMatchesRegularExpression('/^[0-9\n]+$/', (string) file_get_contents($files[0]));
    }

    public function testPurgeRemovesOnlyExpiredFiles(): void
    {
        $this->limiter->hit('203.0.113.10', self::NOW);
        $this->limiter->hit('198.51.100.20', self::NOW + 3000);

        $this->limiter->purgeExpired(self::NOW + 3600);

        self::assertCount(1, glob($this->directory . '/*.hits') ?: []);
    }
}
