<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Http;

use PHPUnit\Framework\TestCase;
use Technum\Http\Response;

final class ResponseTest extends TestCase
{
    public function testHtmlResponseCarriesSecurityAndNoCacheHeaders(): void
    {
        $response = Response::html('<p>Bonjour</p>', 422);

        self::assertSame(422, $response->status);
        self::assertSame('<p>Bonjour</p>', $response->body);
        self::assertSame('text/html; charset=utf-8', $response->header('Content-Type'));
        self::assertSame(Response::CONTENT_SECURITY_POLICY, $response->header('Content-Security-Policy'));
        self::assertStringContainsString("script-src 'self'", Response::CONTENT_SECURITY_POLICY);
        self::assertStringContainsString("frame-ancestors 'none'", Response::CONTENT_SECURITY_POLICY);
        self::assertSame('nosniff', $response->header('X-Content-Type-Options'));
        self::assertSame('strict-origin-when-cross-origin', $response->header('Referrer-Policy'));
        self::assertSame('no-cache, private', $response->header('Cache-Control'));
        self::assertSame('no-cache', $response->header('X-LiteSpeed-Cache-Control'));
    }

    public function testRedirectDefaultsToSeeOther(): void
    {
        $response = Response::redirect('/?envoi=ok#contact');

        self::assertSame(303, $response->status);
        self::assertSame('/?envoi=ok#contact', $response->header('Location'));
        self::assertSame('', $response->body);
    }

    public function testWithHeaderReturnsACopy(): void
    {
        $original = Response::html('x');
        $copy = $original->withHeader('Strict-Transport-Security', 'max-age=31536000');

        self::assertNull($original->header('Strict-Transport-Security'));
        self::assertSame('max-age=31536000', $copy->header('Strict-Transport-Security'));
        self::assertSame('x', $copy->body);
    }
}
