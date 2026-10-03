<?php

declare(strict_types=1);

namespace Technum\Tests\Integration;

use Technum\Page\HomePage;
use Technum\Tests\Support\Html;

final class SeoTest extends ApplicationTestCase
{
    public function testHomePageDescribesItselfForSearchAndSharing(): void
    {
        $html = Html::parse($this->get('/')->body);

        self::assertSame(HomePage::TITLE, $html->text('title'));
        self::assertSame(HomePage::DESCRIPTION, $html->attribute('meta[name="description"]', 'content'));
        self::assertSame('https://bytechnum.com/', $html->attribute('meta[property="og:url"]', 'content'));
        self::assertSame('summary_large_image', $html->attribute('meta[name="twitter:card"]', 'content'));

        $image = $html->attribute('meta[property="og:image"]', 'content');
        self::assertStringStartsWith('https://bytechnum.com/assets/', $image);
        self::assertFileExists(self::ROOT . '/public/' . substr($image, strlen('https://bytechnum.com/')));
    }

    public function testHomePageCarriesOrganizationStructuredData(): void
    {
        $json = Html::parse($this->get('/')->body)->text('script[type="application/ld+json"]');
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        self::assertIsArray($data);
        self::assertSame('Organization', $data['@type']);
    }

    public function testOpenGraphImageHasTheExpectedSize(): void
    {
        $size = getimagesize(self::ROOT . '/public/assets/img/og-image.png');
        if ($size === false) {
            self::fail("L'image de partage est illisible.");
        }

        self::assertSame([1200, 630], [$size[0], $size[1]]);
    }

    public function testRobotsAndSitemapListThePublicPages(): void
    {
        self::assertStringContainsString(
            'Sitemap: https://bytechnum.com/sitemap.xml',
            (string) file_get_contents(self::ROOT . '/public/robots.txt'),
        );

        $sitemap = simplexml_load_file(self::ROOT . '/public/sitemap.xml');
        if ($sitemap === false) {
            self::fail('sitemap.xml est illisible.');
        }
        $locations = [];
        foreach ($sitemap->url as $url) {
            $locations[] = (string) $url->loc;
        }

        self::assertSame([
            'https://bytechnum.com/',
            'https://bytechnum.com/mentions-legales',
            'https://bytechnum.com/confidentialite',
            'https://bytechnum.com/cgu',
        ], $locations);
    }
}
