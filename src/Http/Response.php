<?php

declare(strict_types=1);

namespace Technum\Http;

final class Response
{
    public const CONTENT_SECURITY_POLICY = "default-src 'self'; img-src 'self'; style-src 'self'; "
        . "script-src 'self'; font-src 'self'; connect-src 'self'; form-action 'self'; "
        . "frame-ancestors 'none'; base-uri 'self'; object-src 'none'";

    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        public readonly string $body = '',
        public readonly int $status = 200,
        public readonly array $headers = [],
    ) {
    }

    /**
     * Le HTML n'est jamais mis en cache : la page porte un jeton de formulaire daté.
     */
    public static function html(string $body, int $status = 200): self
    {
        return new self($body, $status, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Security-Policy' => self::CONTENT_SECURITY_POLICY,
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
            'X-Frame-Options' => 'DENY',
            'Cache-Control' => 'no-cache, private',
            'X-LiteSpeed-Cache-Control' => 'no-cache',
        ]);
    }

    public static function redirect(string $location, int $status = 303): self
    {
        return new self('', $status, [
            'Location' => $location,
            'Cache-Control' => 'no-cache, private',
        ]);
    }

    public function header(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function withHeader(string $name, string $value): self
    {
        return new self($this->body, $this->status, [...$this->headers, $name => $value]);
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
        echo $this->body;
    }
}
