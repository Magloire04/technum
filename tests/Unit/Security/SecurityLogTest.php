<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Security;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Technum\Security\SecurityLog;
use Technum\Tests\Support\TempDirectory;

final class SecurityLogTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = TempDirectory::create('technum-log');
    }

    protected function tearDown(): void
    {
        TempDirectory::remove($this->directory);
    }

    public function testRecordsTheEventWithItsUtcTime(): void
    {
        $file = $this->directory . '/logs/security.log';

        (new SecurityLog($file))->record('contact.honeypot', 1_790_000_000);

        self::assertSame(gmdate('Y-m-d\TH:i:s\Z', 1_790_000_000) . " contact.honeypot\n", file_get_contents($file));
    }

    public function testRejectsEventsThatCouldCarryPersonalData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new SecurityLog($this->directory . '/security.log'))->record('contact.refus awa@example.bj', 1);
    }

    public function testRotatesTheFileWhenItGetsTooLarge(): void
    {
        $file = $this->directory . '/security.log';
        file_put_contents($file, str_repeat('x', SecurityLog::MAX_BYTES + 1));

        (new SecurityLog($file))->record('contact.rate_limited', 1_790_000_000);

        self::assertFileExists($file . '.1');
        self::assertLessThan(100, (int) filesize($file));
    }
}
