<?php

declare(strict_types=1);

namespace Technum\Tests\Integration;

/**
 * La politique de confidentialité promet que l'empreinte d'une adresse IP ne reste qu'une heure.
 */
final class RateLimitRetentionTest extends ApplicationTestCase
{
    private function fingerprintWithLastHit(int $secondsAgo): string
    {
        $directory = $this->storageDir . '/rate-limit';
        if (!is_dir($directory)) {
            mkdir($directory, 0750, true);
        }
        $file = $directory . '/' . hash('sha256', (string) $secondsAgo) . '.hits';
        file_put_contents($file, (string) (self::NOW - $secondsAgo));

        return $file;
    }

    public function testVisitingAPageRemovesFingerprintsOlderThanAnHour(): void
    {
        $file = $this->fingerprintWithLastHit(3601);

        $this->get('/');

        self::assertFileDoesNotExist($file);
    }

    public function testRejectedRequestRemovesFingerprintsOlderThanAnHour(): void
    {
        $file = $this->fingerprintWithLastHit(3601);

        $this->post('/contact', ['token' => 'faux']);

        self::assertFileDoesNotExist($file);
    }

    public function testFingerprintFromTheLastHourIsKept(): void
    {
        $file = $this->fingerprintWithLastHit(60);

        $this->get('/');

        self::assertFileExists($file);
    }
}
