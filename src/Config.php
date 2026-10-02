<?php

declare(strict_types=1);

namespace Technum;

/**
 * Configuration lue dans .env et validée au démarrage. Aucun message d'erreur ne contient de secret.
 */
final class Config
{
    private const SMTP_KEYS = ['SMTP_HOST', 'SMTP_PORT', 'SMTP_USERNAME', 'SMTP_PASSWORD'];

    private function __construct(
        public readonly string $appEnv,
        public readonly string $appSecret,
        public readonly string $mailTransport,
        public readonly string $smtpHost,
        public readonly int $smtpPort,
        public readonly string $smtpUsername,
        public readonly string $smtpPassword,
        public readonly string $contactSenderEmail,
        public readonly string $contactRecipientEmail,
        public readonly string $clientIpHeader,
    ) {
    }

    /**
     * @param array<string, string|null> $env
     */
    public static function fromArray(array $env): self
    {
        $read = static fn (string $key): string => trim((string) ($env[$key] ?? ''));
        $appEnv = $read('APP_ENV') !== '' ? $read('APP_ENV') : 'production';
        $mailTransport = $read('MAIL_TRANSPORT') !== '' ? $read('MAIL_TRANSPORT') : 'smtp';
        $problems = [];

        if (strlen($read('APP_SECRET')) < 32) {
            $problems[] = 'APP_SECRET doit contenir au moins 32 caractères';
        }

        if (!in_array($mailTransport, ['smtp', 'log'], true)) {
            $problems[] = 'MAIL_TRANSPORT doit valoir smtp ou log';
        } elseif ($mailTransport === 'log' && $appEnv === 'production') {
            $problems[] = 'MAIL_TRANSPORT=log est interdit en production';
        }

        $port = 0;
        if ($mailTransport === 'smtp') {
            foreach (self::SMTP_KEYS as $key) {
                if ($read($key) === '') {
                    $problems[] = $key . ' est vide';
                }
            }
            $parsedPort = filter_var($read('SMTP_PORT'), FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1, 'max_range' => 65535],
            ]);
            if ($read('SMTP_PORT') !== '' && $parsedPort === false) {
                $problems[] = 'SMTP_PORT doit être un numéro de port';
            }
            $port = $parsedPort === false ? 0 : $parsedPort;
        }

        foreach (['CONTACT_SENDER_EMAIL', 'CONTACT_RECIPIENT_EMAIL'] as $key) {
            if (filter_var($read($key), FILTER_VALIDATE_EMAIL) === false) {
                $problems[] = $key . ' doit être une adresse e-mail valide';
            }
        }

        $clientIpHeader = $read('CLIENT_IP_HEADER');
        if ($clientIpHeader !== '' && preg_match('/^HTTP_[A-Z0-9_]+$/', $clientIpHeader) !== 1) {
            $problems[] = 'CLIENT_IP_HEADER doit ressembler à HTTP_X_FORWARDED_FOR';
        }

        if ($problems !== []) {
            throw new ConfigException('Configuration invalide : ' . implode(', ', $problems) . '.');
        }

        return new self(
            appEnv: $appEnv,
            appSecret: $read('APP_SECRET'),
            mailTransport: $mailTransport,
            smtpHost: $read('SMTP_HOST'),
            smtpPort: $port,
            smtpUsername: $read('SMTP_USERNAME'),
            smtpPassword: (string) ($env['SMTP_PASSWORD'] ?? ''),
            contactSenderEmail: $read('CONTACT_SENDER_EMAIL'),
            contactRecipientEmail: $read('CONTACT_RECIPIENT_EMAIL'),
            clientIpHeader: $clientIpHeader,
        );
    }

    public function isProduction(): bool
    {
        return $this->appEnv === 'production';
    }
}
