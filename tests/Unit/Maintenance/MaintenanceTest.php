<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Maintenance;

use PHPUnit\Framework\TestCase;
use Technum\Maintenance\LogRotator;
use Technum\Maintenance\MailFailureWatch;
use Technum\Maintenance\Maintenance;
use Technum\Security\RateLimiter;
use Technum\Tests\Support\TempDirectory;

final class MaintenanceTest extends TestCase
{
    private const NOW = 1_790_000_000;

    private string $directory;

    protected function setUp(): void
    {
        $this->directory = TempDirectory::create('technum-entretien');
        mkdir($this->directory . '/rate-limit');
    }

    protected function tearDown(): void
    {
        TempDirectory::remove($this->directory);
    }

    private function maintenance(): Maintenance
    {
        return new Maintenance(
            new RateLimiter($this->directory . '/rate-limit', 'secret-de-test'),
            new LogRotator(10),
            new MailFailureWatch($this->directory . '/security.log', $this->directory . '/entretien-etat.txt'),
            $this->directory . '/php-errors.log',
        );
    }

    public function testQuietRunPurgesFingerprintsAndRotatesTheErrorLog(): void
    {
        $fingerprint = $this->directory . '/rate-limit/' . str_repeat('a', 64) . '.hits';
        file_put_contents($fingerprint, (string) (self::NOW - 3601));
        file_put_contents($this->directory . '/php-errors.log', str_repeat('x', 20));

        self::assertSame([], $this->maintenance()->run(self::NOW));
        self::assertFileDoesNotExist($fingerprint);
        self::assertFileExists($this->directory . '/php-errors.log.1');
    }

    public function testFailedRequestsRaiseAnAlert(): void
    {
        file_put_contents($this->directory . '/security.log', "2026-10-03T01:00:00Z contact.mail_failed\n2026-10-03T01:10:00Z contact.mail_failed\n");

        $alert = implode("\n", $this->maintenance()->run(self::NOW));

        self::assertStringContainsString("2 demandes du formulaire n'ont pas pu partir", $alert);
        self::assertSame([], $this->maintenance()->run(self::NOW));
    }
}
