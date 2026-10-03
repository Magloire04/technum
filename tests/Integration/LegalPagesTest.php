<?php

declare(strict_types=1);

namespace Technum\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use Technum\Http\Request;
use Technum\Tests\Support\Html;

final class LegalPagesTest extends ApplicationTestCase
{
    /** @return iterable<string, array{string, string}> */
    public static function pages(): iterable
    {
        yield 'mentions légales' => ['/mentions-legales', 'Mentions légales'];
        yield 'confidentialité' => ['/confidentialite', 'Politique de confidentialité'];
        yield 'conditions' => ['/cgu', "Conditions d'utilisation"];
    }

    #[DataProvider('pages')]
    public function testLegalPageIsServedWithItsCanonicalAddress(string $path, string $title): void
    {
        $response = $this->get($path);
        $html = Html::parse($response->body);

        self::assertSame(200, $response->status);
        self::assertSame($title, $html->text('h1'));
        self::assertSame('https://bytechnum.com' . $path, $html->attribute('link[rel="canonical"]', 'href'));
        self::assertContains('mailto:technum.services@bytechnum.com', $html->attributes('.legal a', 'href'));
    }

    public function testTrailingSlashFromTheBrowserIsAccepted(): void
    {
        $request = Request::fromGlobals(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/cgu/'], [], []);

        self::assertSame(200, $this->application()->handle($request)->status);
    }

    public function testLegalNoticeNamesTheHost(): void
    {
        self::assertStringContainsString('Spaceship, Inc.', Html::parse($this->get('/mentions-legales')->body)->text('.legal'));
    }

    public function testPrivacyPolicyStatesRetentionCookiesAndAuthority(): void
    {
        $text = Html::parse($this->get('/confidentialite')->body)->text('.legal');

        self::assertStringContainsString('12 mois', $text);
        self::assertStringContainsString('aucun cookie', $text);
        self::assertStringContainsString('APDP', $text);
    }

    public function testFooterLinksToEveryLegalPage(): void
    {
        $links = Html::parse($this->get('/')->body)->attributes('.site-footer a', 'href');

        foreach (['/mentions-legales', '/confidentialite', '/cgu'] as $path) {
            self::assertContains($path, $links);
        }
    }
}
