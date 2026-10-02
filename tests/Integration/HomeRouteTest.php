<?php

declare(strict_types=1);

namespace Technum\Tests\Integration;

use Technum\Http\Request;
use Technum\Http\Response;
use Technum\Tests\Support\Html;

final class HomeRouteTest extends ApplicationTestCase
{
    public function testHomePageRespondsWithSecurityHeaders(): void
    {
        $response = $this->get('/');

        self::assertSame(200, $response->status);
        self::assertSame(Response::CONTENT_SECURITY_POLICY, $response->header('Content-Security-Policy'));
        self::assertSame('no-cache, private', $response->header('Cache-Control'));
        self::assertStringContainsString('<html lang="fr"', $response->body);
        self::assertSame('https://bytechnum.com/', Html::parse($response->body)->attribute('link[rel="canonical"]', 'href'));
    }

    public function testHeadRequestIsAnswered(): void
    {
        $response = $this->application()->handle(new Request('HEAD', '/'));

        self::assertSame(200, $response->status);
    }

    public function testContactPathRedirectsToTheContactSection(): void
    {
        $response = $this->get('/contact');

        self::assertSame(301, $response->status);
        self::assertSame('/#contact', $response->header('Location'));
    }

    public function testUnknownPageIsANotFoundPageThatIsNotIndexed(): void
    {
        $response = $this->get('/page-inconnue');
        $html = Html::parse($response->body);

        self::assertSame(404, $response->status);
        self::assertSame("Cette page n'existe pas.", $html->text('h1'));
        self::assertSame('noindex', $html->attribute('meta[name="robots"]', 'content'));
        self::assertSame(0, $html->count('link[rel="canonical"]'));
    }

    public function testOnlyProductionSendsStrictTransportSecurity(): void
    {
        $production = $this->application([
            'APP_ENV' => 'production',
            'MAIL_TRANSPORT' => 'smtp',
            'SMTP_HOST' => 'smtp.example.test',
            'SMTP_PORT' => '465',
            'SMTP_USERNAME' => 'elisee.atonde@bytechnum.com',
            'SMTP_PASSWORD' => 'secret-de-test',
        ]);

        self::assertSame('max-age=31536000', $this->get('/', [], $production)->header('Strict-Transport-Security'));
        self::assertNull($this->get('/')->header('Strict-Transport-Security'));
    }
}
