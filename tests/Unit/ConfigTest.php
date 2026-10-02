<?php

declare(strict_types=1);

namespace Technum\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Technum\Config;
use Technum\ConfigException;

final class ConfigTest extends TestCase
{
    /** @return array<string, string> */
    private static function smtpEnv(): array
    {
        return [
            'APP_ENV' => 'production',
            'APP_SECRET' => str_repeat('s', 64),
            'MAIL_TRANSPORT' => 'smtp',
            'SMTP_HOST' => 'smtp.example.test',
            'SMTP_PORT' => '465',
            'SMTP_USERNAME' => 'elisee.atonde@bytechnum.com',
            'SMTP_PASSWORD' => 'mot-de-passe-secret',
            'CONTACT_SENDER_EMAIL' => 'elisee.atonde@bytechnum.com',
            'CONTACT_RECIPIENT_EMAIL' => 'elisee.atonde@bytechnum.com',
        ];
    }

    public function testValidSmtpConfiguration(): void
    {
        $config = Config::fromArray(self::smtpEnv());

        self::assertTrue($config->isProduction());
        self::assertSame('smtp', $config->mailTransport);
        self::assertSame('smtp.example.test', $config->smtpHost);
        self::assertSame(465, $config->smtpPort);
        self::assertSame('mot-de-passe-secret', $config->smtpPassword);
        self::assertSame('', $config->clientIpHeader);
    }

    public function testDefaultsAreProductionAndSmtp(): void
    {
        $env = self::smtpEnv();
        unset($env['APP_ENV'], $env['MAIL_TRANSPORT']);

        $config = Config::fromArray($env);

        self::assertSame('production', $config->appEnv);
        self::assertSame('smtp', $config->mailTransport);
    }

    public function testLocalLogTransportDoesNotNeedSmtp(): void
    {
        $config = Config::fromArray([
            'APP_ENV' => 'local',
            'APP_SECRET' => str_repeat('s', 32),
            'MAIL_TRANSPORT' => 'log',
            'CONTACT_SENDER_EMAIL' => 'elisee.atonde@bytechnum.com',
            'CONTACT_RECIPIENT_EMAIL' => 'elisee.atonde@bytechnum.com',
        ]);

        self::assertFalse($config->isProduction());
        self::assertSame('log', $config->mailTransport);
        self::assertSame(0, $config->smtpPort);
    }

    public function testClientIpHeaderIsKept(): void
    {
        $config = Config::fromArray([...self::smtpEnv(), 'CLIENT_IP_HEADER' => 'HTTP_X_FORWARDED_FOR']);

        self::assertSame('HTTP_X_FORWARDED_FOR', $config->clientIpHeader);
    }

    /** @return iterable<string, array{array<string, string>, string}> */
    public static function invalidEnvironments(): iterable
    {
        $base = self::smtpEnv();

        yield 'secret trop court' => [[...$base, 'APP_SECRET' => 'court'], 'APP_SECRET doit contenir au moins 32 caractères'];
        yield 'hôte SMTP absent' => [[...$base, 'SMTP_HOST' => ''], 'SMTP_HOST est vide'];
        yield 'port invalide' => [[...$base, 'SMTP_PORT' => 'abc'], 'SMTP_PORT doit être un numéro de port'];
        yield 'journal en production' => [[...$base, 'MAIL_TRANSPORT' => 'log'], 'MAIL_TRANSPORT=log est interdit en production'];
        yield 'transport inconnu' => [[...$base, 'MAIL_TRANSPORT' => 'pigeon'], 'MAIL_TRANSPORT doit valoir smtp ou log'];
        yield 'destinataire invalide' => [[...$base, 'CONTACT_RECIPIENT_EMAIL' => 'pas-une-adresse'], 'CONTACT_RECIPIENT_EMAIL doit être une adresse e-mail valide'];
        yield 'en-tête IP invalide' => [[...$base, 'CLIENT_IP_HEADER' => 'X-Forwarded-For'], 'CLIENT_IP_HEADER doit ressembler à HTTP_X_FORWARDED_FOR'];
    }

    /**
     * @param array<string, string> $env
     */
    #[DataProvider('invalidEnvironments')]
    public function testInvalidConfigurationIsRejectedWithAClearMessage(array $env, string $expectedProblem): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage($expectedProblem);

        Config::fromArray($env);
    }

    public function testErrorMessageNeverContainsThePassword(): void
    {
        try {
            Config::fromArray([...self::smtpEnv(), 'SMTP_HOST' => '']);
            self::fail('Une exception était attendue.');
        } catch (ConfigException $exception) {
            self::assertStringNotContainsString('mot-de-passe-secret', $exception->getMessage());
        }
    }
}
