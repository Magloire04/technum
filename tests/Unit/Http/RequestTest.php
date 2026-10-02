<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Http;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Technum\Http\Request;

final class RequestTest extends TestCase
{
    /** @return iterable<string, array{string, string}> */
    public static function paths(): iterable
    {
        yield 'racine' => ['/', '/'];
        yield 'vide' => ['', '/'];
        yield 'barre finale' => ['/cgu/', '/cgu'];
        yield 'barres multiples' => ['//cgu//', '/cgu'];
        yield 'sous-chemin' => ['/a/b/', '/a/b'];
    }

    #[DataProvider('paths')]
    public function testNormalizePathRemovesSurroundingSlashes(string $input, string $expected): void
    {
        self::assertSame($expected, Request::normalizePath($input));
    }

    public function testFromGlobalsReadsMethodPathQueryAndBody(): void
    {
        $request = Request::fromGlobals(
            ['REQUEST_METHOD' => 'post', 'REQUEST_URI' => '/contact/?envoi=ok', 'REMOTE_ADDR' => '203.0.113.5'],
            ['envoi' => 'ok', 'liste' => ['a']],
            ['name' => 'Awa'],
        );

        self::assertSame('POST', $request->method);
        self::assertSame('/contact', $request->path);
        self::assertSame(['envoi' => 'ok'], $request->query);
        self::assertSame('203.0.113.5', $request->clientIp);
        self::assertSame('Awa', $request->input('name'));
    }

    public function testInputReturnsEmptyStringForMissingOrNonTextValues(): void
    {
        $request = new Request('POST', '/contact', [], ['name' => ['x']]);

        self::assertSame('', $request->input('name'));
        self::assertSame('', $request->input('email'));
    }

    public function testClientIpComesFromLastValidAddressOfConfiguredHeader(): void
    {
        $request = Request::fromGlobals(
            ['REMOTE_ADDR' => '10.0.0.1', 'HTTP_X_FORWARDED_FOR' => '198.51.100.7, 203.0.113.9'],
            [],
            [],
            'HTTP_X_FORWARDED_FOR',
        );

        self::assertSame('203.0.113.9', $request->clientIp);
    }

    public function testClientIpFallsBackToRemoteAddrWhenHeaderIsInvalid(): void
    {
        $request = Request::fromGlobals(
            ['REMOTE_ADDR' => '10.0.0.1', 'HTTP_X_FORWARDED_FOR' => 'inconnu'],
            [],
            [],
            'HTTP_X_FORWARDED_FOR',
        );

        self::assertSame('10.0.0.1', $request->clientIp);
    }

    public function testClientIpIgnoresHeaderWhenNotConfigured(): void
    {
        $request = Request::fromGlobals(
            ['REMOTE_ADDR' => '10.0.0.1', 'HTTP_X_FORWARDED_FOR' => '203.0.113.9'],
            [],
            [],
        );

        self::assertSame('10.0.0.1', $request->clientIp);
    }
}
