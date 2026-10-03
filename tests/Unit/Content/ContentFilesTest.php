<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Content;

use FilesystemIterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Technum\Content\ContentRepository;
use Technum\Content\Product;
use Technum\Content\ProductStage;

/**
 * Contrôle le contenu réel du site par rapport à la spécification validée.
 */
final class ContentFilesTest extends TestCase
{
    private const ROOT = __DIR__ . '/../../..';

    private ContentRepository $content;

    protected function setUp(): void
    {
        $this->content = new ContentRepository(self::ROOT . '/content', self::ROOT . '/public');
    }

    public function testProductsAreTheFourPublicProductsInOrder(): void
    {
        $products = $this->content->products();

        self::assertSame(
            ['oeil360-finance', 'dis-oui', 'provia', 'carte-uac'],
            array_map(static fn (Product $product): string => $product->slug, $products),
        );
        self::assertSame(
            [ProductStage::Live, ProductStage::Live, ProductStage::Beta, ProductStage::Pilot],
            array_map(static fn (Product $product): ProductStage => $product->stage, $products),
        );
    }

    public function testStableProductsHaveNoWorkInProgress(): void
    {
        $bySlug = [];
        foreach ($this->content->products() as $product) {
            $bySlug[$product->slug] = $product;
        }

        self::assertTrue($bySlug['oeil360-finance']->isStable());
        self::assertTrue($bySlug['dis-oui']->isStable());
        self::assertFalse($bySlug['provia']->isStable());
        self::assertFalse($bySlug['carte-uac']->isStable());
    }

    public function testEveryProductHasAShortSummary(): void
    {
        self::assertSame(
            [
                'Revenus, dépenses et comptes en franc CFA, au même endroit.',
                'Une demande de rendez-vous transformée en petit jeu.',
                "Stages\u{00A0}: étudiants et entreprises. Projet en cours.",
                "Le campus d'Abomey-Calavi, à pied, même sans réseau.",
            ],
            array_map(static fn (Product $product): string => $product->summary, $this->content->products()),
        );
    }

    public function testContactDetailsMatchTheValidatedSpecification(): void
    {
        $site = $this->content->site();

        self::assertSame('technum.services@bytechnum.com', $site->email);
        self::assertSame('+229 01 50 61 73 00', $site->phoneDisplay);
        self::assertSame('+2290150617300', $site->phoneE164);
        self::assertSame('2290150617300', $site->whatsappNumber);
        self::assertSame('https://github.com/Magloire04', $site->githubUrl);
    }

    public function testThereAreFourProjectsAndFiveServices(): void
    {
        self::assertCount(4, $this->content->projects());
        self::assertCount(5, $this->content->services());
    }

    public function testServiceExamplesPointToExistingAnchors(): void
    {
        $anchors = ['produits'];
        foreach ($this->content->products() as $product) {
            $anchors[] = $product->anchor();
        }
        foreach ($this->content->projects() as $project) {
            $anchors[] = $project->anchor();
        }

        foreach ($this->content->services() as $service) {
            foreach ($service->examples as $example) {
                if ($example->hasLink()) {
                    self::assertContains(ltrim($example->href, '#'), $anchors, $service->name . ', ' . $example->label);
                }
            }
        }
    }

    /**
     * Empreintes SHA-256 des mots qui désignent les deux mandats clients confidentiels.
     * Les mots eux-mêmes ne figurent nulle part dans le dépôt public.
     */
    private const CONFIDENTIAL_WORD_HASHES = [
        '196a3b33b94a4520b787250ea4a118e7bd4230cbb501b02f8cef0a816546f309',
        '12529171df4457d3621113a39f44cb35d54083b091f372df7075c666d8c56a35',
        '7567ee35c5ae9d23fa3b1eedce34aa9218f6cb7c32f28d14bcf300ef6030c538',
    ];

    public function testConfidentialWordDetectionWorks(): void
    {
        self::assertSame([hash('sha256', 'exemple')], self::matchingHashes('Un Exemple de texte.', [hash('sha256', 'exemple')]));
        self::assertSame([], self::matchingHashes('Un texte ordinaire.', [hash('sha256', 'exemple')]));
    }

    public function testPublishedFilesNeverNameTheConfidentialClients(): void
    {
        foreach (self::publishedTextFiles() as $file) {
            $found = self::matchingHashes((string) file_get_contents($file), self::CONFIDENTIAL_WORD_HASHES);
            self::assertSame([], $found, 'Mot confidentiel dans ' . $file);
        }
    }

    /** @return iterable<string, array{string}> */
    public static function forbiddenPatterns(): iterable
    {
        yield 'dossier ai-learning' => ['/ai-learning/i'];
        yield 'tiret cadratin' => ['/\x{2014}/u'];
        yield 'tiret demi-cadratin' => ['/\x{2013}/u'];
    }

    #[DataProvider('forbiddenPatterns')]
    public function testPublishedFilesNeverContainForbiddenText(string $pattern): void
    {
        foreach (self::publishedTextFiles() as $file) {
            self::assertDoesNotMatchRegularExpression($pattern, (string) file_get_contents($file), 'Texte interdit dans ' . $file);
        }
    }

    /**
     * Empreintes de la liste fournie qui correspondent à un mot du texte, sans tenir compte de la casse.
     *
     * @param list<string> $hashes
     *
     * @return list<string>
     */
    private static function matchingHashes(string $text, array $hashes): array
    {
        $words = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($text)) ?: [];
        $wordHashes = array_map(static fn (string $word): string => hash('sha256', $word), array_unique($words));

        return array_values(array_intersect($hashes, $wordHashes));
    }

    /**
     * Fichiers texte publiés par le site, hors licences de polices tierces.
     *
     * @return list<string>
     */
    private static function publishedTextFiles(): array
    {
        $files = [];
        foreach (['content', 'templates', 'public'] as $directory) {
            $path = self::ROOT . '/' . $directory;
            if (!is_dir($path)) {
                continue;
            }
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
            foreach ($iterator as $file) {
                if (!$file instanceof SplFileInfo || str_contains($file->getPathname(), 'fonts')) {
                    continue;
                }
                if (in_array($file->getExtension(), ['php', 'html', 'css', 'js', 'txt', 'xml', 'svg'], true)) {
                    $files[] = $file->getPathname();
                }
            }
        }

        return $files;
    }
}
