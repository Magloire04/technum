<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Content;

use PHPUnit\Framework\TestCase;
use Technum\Content\Product;
use Technum\Content\ProductImage;
use Technum\Content\ProductStage;
use Technum\Content\Project;
use Technum\Content\Service;
use Technum\Content\ServiceExample;

final class ContentModelTest extends TestCase
{
    private static function product(string $next): Product
    {
        return new Product(
            slug: 'oeil360-finance',
            name: 'Oeil 360° Finance',
            url: 'https://oeil360finance.bytechnum.com',
            tagline: 'Suivre ses comptes.',
            audience: 'Particuliers.',
            stage: ProductStage::Live,
            done: 'Comptes multiples.',
            next: $next,
            note: '',
            icon: 'img/produits/oeil360-finance-icone.png',
            image: new ProductImage('img/a.webp', '', 1280, 800, 'Capture', 'desktop'),
        );
    }

    public function testProductLinksAndAnchor(): void
    {
        $product = self::product('');

        self::assertSame('oeil360finance.bytechnum.com', $product->host());
        self::assertSame('https://oeil360finance.bytechnum.com/?ref=bytechnum', $product->trackedUrl());
        self::assertSame('produit-oeil360-finance', $product->anchor());
    }

    public function testProductWithoutAddressHasNoPublicAccess(): void
    {
        $product = new Product(
            slug: 'provia',
            name: 'PROVIA',
            url: '',
            tagline: 'Des stages.',
            audience: 'Étudiants.',
            stage: ProductStage::Beta,
            done: 'Profils.',
            next: 'Inscriptions.',
            note: '',
            icon: 'img/produits/provia-icone.svg',
            image: new ProductImage('img/a.webp', '', 1280, 800, 'Capture', 'desktop'),
        );

        self::assertFalse($product->hasPublicAccess());
        self::assertSame('', $product->host());
        self::assertTrue(self::product('')->hasPublicAccess());
    }

    public function testProductWithoutNextStepIsStable(): void
    {
        self::assertTrue(self::product('')->isStable());
        self::assertFalse(self::product('Version mobile.')->isStable());
    }

    public function testProjectLink(): void
    {
        $withLink = new Project('tracacajou', 'TraçaCajou', 'Certificats.', 'Preuve de concept', 'https://github.com/Magloire04/TracaCajou', 'Voir le dépôt');
        $withoutLink = new Project('bescat', 'BESCAT', 'Refonte.', 'Mandat client', '', '');

        self::assertSame('realisation-tracacajou', $withLink->anchor());
        self::assertTrue($withLink->hasLink());
        self::assertFalse($withoutLink->hasLink());
    }

    public function testServiceExamplesLabelFollowsCount(): void
    {
        $one = new Service('Applications de gestion', 'Des outils.', [new ServiceExample('Oeil 360° Finance', '#produit-oeil360-finance')]);
        $two = new Service('Plateformes en ligne', 'Des services.', [new ServiceExample('PROVIA', '#produit-provia'), new ServiceExample('BESCAT', '')]);

        self::assertSame('Exemple', $one->examplesLabel());
        self::assertSame('Exemples', $two->examplesLabel());
        self::assertFalse($two->examples[1]->hasLink());
    }
}
