<?php

declare(strict_types=1);

namespace Technum\Http;

/**
 * Requête HTTP réduite à ce dont le site a besoin.
 */
final class Request
{
    /**
     * @param array<string, string>   $query
     * @param array<array-key, mixed> $body
     */
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $query = [],
        public readonly array $body = [],
        public readonly string $clientIp = '',
    ) {
    }

    /**
     * @param array<array-key, mixed> $server
     * @param array<array-key, mixed> $query
     * @param array<array-key, mixed> $body
     */
    public static function fromGlobals(array $server, array $query, array $body, string $clientIpHeader = ''): self
    {
        $uriPath = parse_url(self::text($server['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
        $textQuery = [];
        foreach ($query as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $textQuery[$key] = $value;
            }
        }

        return new self(
            strtoupper(self::text($server['REQUEST_METHOD'] ?? 'GET')),
            self::normalizePath(is_string($uriPath) ? $uriPath : '/'),
            $textQuery,
            $body,
            self::resolveClientIp($server, $clientIpHeader),
        );
    }

    public static function normalizePath(string $path): string
    {
        $trimmed = trim($path, '/');

        return $trimmed === '' ? '/' : '/' . $trimmed;
    }

    public function input(string $key): string
    {
        return self::text($this->body[$key] ?? '');
    }

    /**
     * Derrière un proxy, la dernière adresse de l'en-tête configuré est celle que le proxy a vue.
     *
     * @param array<array-key, mixed> $server
     */
    private static function resolveClientIp(array $server, string $clientIpHeader): string
    {
        if ($clientIpHeader !== '') {
            $addresses = array_map('trim', explode(',', self::text($server[$clientIpHeader] ?? '')));
            $last = $addresses[array_key_last($addresses)];
            if (filter_var($last, FILTER_VALIDATE_IP) !== false) {
                return $last;
            }
        }

        return self::text($server['REMOTE_ADDR'] ?? '');
    }

    private static function text(mixed $value): string
    {
        return is_string($value) ? $value : '';
    }
}
