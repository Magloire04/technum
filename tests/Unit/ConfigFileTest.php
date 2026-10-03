<?php

declare(strict_types=1);

namespace Technum\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Technum\Config;
use Technum\ConfigException;
use Technum\Tests\Support\TempDirectory;

/**
 * Lecture du vrai fichier .env : guillemets, erreur de syntaxe, et aucune valeur recopiée dans un message.
 */
final class ConfigFileTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = TempDirectory::create('technum-env');
    }

    protected function tearDown(): void
    {
        TempDirectory::remove($this->directory);
    }

    private function writeEnv(string $passwordLine): void
    {
        file_put_contents($this->directory . '/.env', implode("\n", [
            'APP_ENV=production',
            'APP_SECRET=' . str_repeat('s', 64),
            'MAIL_TRANSPORT=smtp',
            'SMTP_HOST=smtp.example.test',
            'SMTP_PORT=465',
            'SMTP_USERNAME=elisee.atonde@bytechnum.com',
            $passwordLine,
            'CONTACT_SENDER_EMAIL=elisee.atonde@bytechnum.com',
            'CONTACT_RECIPIENT_EMAIL=elisee.atonde@bytechnum.com',
        ]) . "\n");
    }

    public function testPasswordBetweenSingleQuotesIsReadAsWritten(): void
    {
        $this->writeEnv("SMTP_PASSWORD='Ab3#x Y9\$z'");

        self::assertSame('Ab3#x Y9$z', Config::fromEnvFile($this->directory)->smtpPassword);
    }

    public function testUnreadableFileNeverRepeatsTheValueInItsMessage(): void
    {
        $this->writeEnv('SMTP_PASSWORD=Ab3 secret');

        try {
            Config::fromEnvFile($this->directory);
            self::fail('Le fichier aurait dû être refusé.');
        } catch (ConfigException $exception) {
            self::assertStringNotContainsString('secret', $exception->getMessage());
            self::assertStringContainsString('guillemets', $exception->getMessage());
        }
    }

    public function testEnvExampleShowsThePasswordBetweenSingleQuotes(): void
    {
        $example = (string) file_get_contents(__DIR__ . '/../../.env.example');

        self::assertMatchesRegularExpression("/^SMTP_PASSWORD=''\$/m", $example);
    }
}
