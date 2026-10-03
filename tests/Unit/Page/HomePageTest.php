<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Page;

use PHPUnit\Framework\TestCase;
use Technum\Contact\ContactFormState;
use Technum\Content\ContentRepository;
use Technum\Page\HomePage;
use Technum\Tests\Support\Html;
use Technum\View\View;

final class HomePageTest extends TestCase
{
    private const ROOT = __DIR__ . '/../../..';

    private string $source;

    private Html $html;

    protected function setUp(): void
    {
        $content = new ContentRepository(self::ROOT . '/content', self::ROOT . '/public');
        $view = new View(self::ROOT . '/templates', self::ROOT . '/public');
        $view->share(['site' => $content->site(), 'products' => $content->products()]);
        $this->source = (new HomePage($view, $content))->render(new ContactFormState('jeton-de-test'));
        $this->html = Html::parse($this->source);
    }

    public function testHeroCarriesTheBrandPromiseAndTwoActions(): void
    {
        self::assertSame('Des solutions numériques conçues pour vos réalités.', $this->html->text('h1'));
        self::assertSame('#contact', $this->html->attribute('.hero__actions .button--primary', 'href'));
        self::assertSame('#produits', $this->html->attribute('.hero__more', 'href'));
    }

    public function testMenuOffersTheContactOnSmallScreens(): void
    {
        self::assertSame('Parler de votre projet', $this->html->text('.site-nav__list .site-nav__contact a'));
        self::assertSame('/#contact', $this->html->attribute('.site-nav__list .site-nav__contact a', 'href'));
    }

    public function testRegisterListsEachProductWithItsStage(): void
    {
        self::assertSame(['Oeil 360° Finance', 'Dis oui', 'PROVIA', 'Carte UAC'], $this->html->texts('.register__name'));
        self::assertSame(['En service', 'En service', 'Bêta', 'Pilote'], $this->html->texts('.register__stage'));
        self::assertSame('Mis à jour le 2 octobre 2026', $this->html->text('.register__updated'));
        self::assertSame(3, $this->html->count('.register__item:nth-child(3) .track__bit--done'));
    }

    public function testProductBlocksShowStateAndLinkToTheProduct(): void
    {
        self::assertSame(4, $this->html->count('.product'));
        self::assertSame('Bêta', $this->html->text('#produit-provia [aria-current="step"]'));
        self::assertSame('Version stable, maintenue.', $this->html->text('#produit-oeil360-finance .product__facts div:nth-child(2) dd'));
        self::assertSame('https://provia.bytechnum.com/?ref=bytechnum', $this->html->attribute('#produit-provia .product__action', 'href'));
        self::assertStringContainsString('données de démonstration', $this->html->text('#produit-carte-uac .product__note'));
    }

    public function testProjectsServicesAndStepsAreListed(): void
    {
        self::assertSame(6, $this->html->count('.project'));
        self::assertSame(0, $this->html->count('#realisation-bescat a'));
        self::assertSame(5, $this->html->count('.service'));
        self::assertSame(5, $this->html->count('.step'));
        self::assertStringContainsString('PROVIA et Carte UAC', $this->html->text('.service:nth-child(2) .service__examples'));
    }

    public function testDirectContactUsesTheValidatedDetails(): void
    {
        self::assertStringStartsWith('https://wa.me/2290150617300?text=', $this->html->attribute('.contact-direct__action', 'href'));
        self::assertContains('tel:+2290150617300', $this->html->attributes('.contact-direct a', 'href'));
        self::assertContains('mailto:elisee.atonde@bytechnum.com', $this->html->attributes('.contact-direct a', 'href'));
    }

    public function testDirectContactEmailCanOnlyWrapBeforeTheAtSign(): void
    {
        self::assertStringContainsString('>elisee.atonde<wbr>@bytechnum.com</a>', $this->source);
    }

    public function testFooterEmailCanOnlyWrapBeforeTheAtSign(): void
    {
        $link = $this->html->elements('.site-footer a[href^="mailto:"]')[0];

        self::assertSame('elisee.atonde<wbr>@bytechnum.com', $link->innerHTML);
    }

    public function testPageRespectsTheContentSecurityPolicy(): void
    {
        self::assertSame(0, $this->html->count('[style]'));
        self::assertSame(0, $this->html->count('style'));
        foreach ($this->html->elements('script') as $script) {
            self::assertTrue($script->hasAttribute('src') || $script->getAttribute('type') === 'application/ld+json');
        }
    }

    public function testFontPreloadUsesTheSameAddressAsTheStylesheet(): void
    {
        $stylesheet = (string) file_get_contents(self::ROOT . '/public/assets/css/site.css');

        self::assertSame('/assets/fonts/montserrat-700.woff2', $this->html->attribute('link[rel="preload"][as="font"]', 'href'));
        self::assertStringContainsString("url('../fonts/montserrat-700.woff2')", $stylesheet);
    }

    public function testEveryAssetReferencedByThePageExists(): void
    {
        preg_match_all('#/assets/([^"\'?\s,]+)#', $this->source, $matches);

        self::assertNotEmpty($matches[1]);
        foreach (array_unique($matches[1]) as $path) {
            self::assertFileExists(self::ROOT . '/public/assets/' . $path);
        }
    }
}
