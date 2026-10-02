<?php

declare(strict_types=1);

namespace Technum\Contact;

use InvalidArgumentException;

/**
 * Jeton signé qui date l'affichage du formulaire. Il remplace une session : le site ne dépose aucun cookie.
 */
final class FormToken
{
    public const MIN_AGE_SECONDS = 3;

    public const MAX_AGE_SECONDS = 7200;

    public function __construct(private readonly string $secret)
    {
        if (strlen($secret) < 32) {
            throw new InvalidArgumentException('Le secret du formulaire doit contenir au moins 32 caractères.');
        }
    }

    public function issue(int $now): string
    {
        $timestamp = (string) $now;

        return $timestamp . '.' . $this->sign($timestamp);
    }

    public function verify(string $token, int $now): TokenStatus
    {
        $parts = explode('.', $token);
        if (count($parts) !== 2 || !ctype_digit($parts[0]) || !hash_equals($this->sign($parts[0]), $parts[1])) {
            return TokenStatus::Invalid;
        }
        $age = $now - (int) $parts[0];
        if ($age < self::MIN_AGE_SECONDS) {
            return TokenStatus::TooFast;
        }
        if ($age > self::MAX_AGE_SECONDS) {
            return TokenStatus::Expired;
        }

        return TokenStatus::Valid;
    }

    private function sign(string $timestamp): string
    {
        return hash_hmac('sha256', 'contact-form|' . $timestamp, $this->secret);
    }
}
