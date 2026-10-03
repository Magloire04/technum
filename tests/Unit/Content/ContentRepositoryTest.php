<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Content;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Technum\Content\ContentException;
use Technum\Content\ContentRepository;
use Technum\Content\ProductStage;
use Technum\Tests\Support\TempDirectory;

final class ContentRepositoryTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = TempDirectory::create('technum-content');
        mkdir($this->root . '/content');
        mkdir($this->root . '/public/assets/img', 0777, true);
        touch($this->root . '/public/assets/img/capture.webp');
        touch($this->root . '/public/assets/img/icone.svg');
    }

    protected function tearDown(): void
    {
        TempDirectory::remove($this->root);
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function write(string $name, array $data): void
    {
        file_put_contents($this->root . '/content/' . $name . '.php', "<?php\n\nreturn " . var_export($data, true) . ";\n");
    }

    private function repository(): ContentRepository
    {
        return new ContentRepository($this->root . '/content', $this->root . '/public');
    }

    /**
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private static function product(array $overrides = []): array
    {
        return [...[
            'slug' => 'exemple',
            'name' => 'Exemple',
            'url' => 'https://exemple.bytechnum.com',
            'tagline' => 'Une accroche : simple.',
            'summary' => 'Un résumé : court.',
            'audience' => 'Tout le monde.',
            'stage' => 'beta',
            'done' => 'Déjà fait.',
            'next' => 'À venir.',
            'note' => '',
            'icon' => 'img/icone.svg',
            'image' => self::image(),
        ], ...$overrides];
    }

    /**
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private static function image(array $overrides = []): array
    {
        return [...[
            'src' => 'img/capture.webp',
            'srcSmall' => '',
            'width' => 1280,
            'height' => 800,
            'alt' => 'Capture',
            'frame' => 'desktop',
        ], ...$overrides];
    }

    public function testBuildsProductsWithFrenchTypography(): void
    {
        $this->write('products', [self::product()]);

        $product = $this->repository()->products()[0];

        self::assertSame('exemple', $product->slug);
        self::assertSame(ProductStage::Beta, $product->stage);
        self::assertSame("Une accroche\u{00A0}: simple.", $product->tagline);
        self::assertSame("Un résumé\u{00A0}: court.", $product->summary);
        self::assertSame('img/capture.webp', $product->image->src);
    }

    public function testProductWithoutAddressIsAccepted(): void
    {
        $this->write('products', [self::product(['url' => ''])]);

        self::assertFalse($this->repository()->products()[0]->hasPublicAccess());
    }

    /** @return iterable<string, array{array<string, mixed>, string}> */
    public static function invalidProducts(): iterable
    {
        yield 'adresse http' => [['url' => 'http://exemple.bytechnum.com'], '« url » doit être une adresse https valide'];
        yield 'état inconnu' => [['stage' => 'lancé'], '« stage » doit valoir conception, pilote, beta ou en-service'];
        yield 'nom vide' => [['name' => ' '], '« name » est vide'];
        yield 'résumé vide' => [['summary' => ' '], '« summary » est vide'];
        yield 'résumé trop long' => [['summary' => str_repeat('a', 71)], '« summary » compte 70 caractères au plus'];
        yield 'icône absente' => [['icon' => 'img/absente.svg'], 'fichier introuvable, assets/img/absente.svg'];
        yield 'slug invalide' => [['slug' => 'Mon Produit'], '« slug » ne contient que des minuscules'];
        yield 'cadre inconnu' => [['image' => self::image(['frame' => 'tablette'])], '« frame » doit valoir desktop ou phone'];
        yield 'texte alternatif vide' => [['image' => self::image(['alt' => ''])], '« alt » est vide'];
        yield 'largeur nulle' => [['image' => self::image(['width' => 0])], '« width » doit être un entier positif'];
    }

    /**
     * @param array<string, mixed> $overrides
     */
    #[DataProvider('invalidProducts')]
    public function testRejectsInvalidProducts(array $overrides, string $expected): void
    {
        $this->write('products', [self::product($overrides)]);

        $this->expectException(ContentException::class);
        $this->expectExceptionMessage($expected);

        $this->repository()->products();
    }

    public function testRejectsMissingFile(): void
    {
        $this->expectException(ContentException::class);
        $this->expectExceptionMessage('Fichier de contenu introuvable : products.php');

        $this->repository()->products();
    }

    public function testRejectsEmptyList(): void
    {
        $this->write('products', []);

        $this->expectException(ContentException::class);
        $this->expectExceptionMessage('products.php doit renvoyer une liste non vide');

        $this->repository()->products();
    }

    public function testProjectLinkNeedsALabel(): void
    {
        $this->write('projects', [[
            'slug' => 'exemple',
            'name' => 'Exemple',
            'description' => 'Description.',
            'nature' => 'Preuve de concept',
            'linkUrl' => 'https://github.com/Magloire04/exemple',
            'linkLabel' => '',
        ]]);

        $this->expectException(ContentException::class);
        $this->expectExceptionMessage('« linkUrl » et « linkLabel » vont ensemble');

        $this->repository()->projects();
    }

    public function testServiceExamplesMustBePageAnchors(): void
    {
        $this->write('services', [[
            'name' => 'Service',
            'description' => 'Description.',
            'examples' => [['label' => 'Ailleurs', 'href' => 'https://ailleurs.test']],
        ]]);

        $this->expectException(ContentException::class);
        $this->expectExceptionMessage('« href » doit être une ancre de la page');

        $this->repository()->services();
    }

    public function testSiteDateMustUseIsoFormat(): void
    {
        $this->write('site', [
            'email' => 'elisee.atonde@bytechnum.com',
            'phoneDisplay' => '+229 01 50 61 73 00',
            'phoneE164' => '+2290150617300',
            'whatsappNumber' => '2290150617300',
            'whatsappMessage' => 'Bonjour.',
            'githubUrl' => 'https://github.com/Magloire04',
            'city' => 'Porto-Novo, Bénin',
            'updatedAt' => '02/10/2026',
        ]);

        $this->expectException(ContentException::class);
        $this->expectExceptionMessage('« updatedAt » doit suivre le format AAAA-MM-JJ');

        $this->repository()->site();
    }

    public function testContentIsLoadedOnlyOnce(): void
    {
        $this->write('products', [self::product()]);
        $repository = $this->repository();
        $repository->products();

        unlink($this->root . '/content/products.php');

        self::assertCount(1, $repository->products());
    }
}
