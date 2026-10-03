<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Maintenance;

use PHPUnit\Framework\TestCase;
use Technum\Maintenance\MailFailureWatch;
use Technum\Tests\Support\TempDirectory;

final class MailFailureWatchTest extends TestCase
{
    private string $directory;

    private string $log;

    protected function setUp(): void
    {
        $this->directory = TempDirectory::create('technum-veille');
        $this->log = $this->directory . '/security.log';
    }

    protected function tearDown(): void
    {
        TempDirectory::remove($this->directory);
    }

    private function watch(): MailFailureWatch
    {
        return new MailFailureWatch($this->log, $this->directory . '/entretien-etat.txt');
    }

    public function testCountsOnlyFailuresNotedSinceTheLastCheck(): void
    {
        file_put_contents($this->log, implode("\n", [
            '2026-10-03T01:00:00Z contact.mail_failed',
            '2026-10-03T01:05:00Z contact.too_fast',
            '2026-10-03T01:10:00Z contact.mail_failed',
        ]) . "\n");

        self::assertSame(2, $this->watch()->newFailures());
        self::assertSame(0, $this->watch()->newFailures());

        file_put_contents($this->log, "2026-10-03T02:00:00Z contact.mail_failed\n", FILE_APPEND);

        self::assertSame(1, $this->watch()->newFailures());
    }

    public function testReadsThePreviousLogAfterARotation(): void
    {
        file_put_contents($this->log . '.1', "2026-10-03T01:00:00Z contact.mail_failed\n");
        file_put_contents($this->log, "2026-10-03T01:30:00Z contact.honeypot\n");

        self::assertSame(1, $this->watch()->newFailures());
    }

    public function testWithoutAnyLogThereIsNoFailure(): void
    {
        self::assertSame(0, $this->watch()->newFailures());
    }
}
