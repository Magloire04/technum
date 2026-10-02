<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\View;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Technum\View\View;

final class ViewTest extends TestCase
{
    private const FIXTURES = __DIR__ . '/../../Fixtures';

    private View $view;

    protected function setUp(): void
    {
        $this->view = new View(self::FIXTURES . '/templates', self::FIXTURES . '/public');
    }

    public function testEscapeFunctionNeutralizesHtmlAndQuotes(): void
    {
        self::assertSame(
            '&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt; &amp; l&apos;eau',
            e('<script>alert("x")</script> & l\'eau'),
        );
        self::assertSame('', e(null));
        self::assertSame('42', e(42));
    }

    public function testRenderEscapesData(): void
    {
        $html = $this->view->render('greeting', ['name' => '<b>Awa</b>']);

        self::assertSame('<p class="greeting">Bonjour &lt;b&gt;Awa&lt;/b&gt;</p>', trim($html));
    }

    public function testSharedDataIsAvailableAndCanBeOverridden(): void
    {
        $this->view->share(['site' => 'TECHNUM', 'name' => 'partagé']);

        $html = $this->view->render('greeting', ['name' => 'Awa']);

        self::assertSame('<p class="greeting">Bonjour Awa de TECHNUM</p>', trim($html));
    }

    public function testTemplatesCanRenderPartials(): void
    {
        self::assertStringContainsString('Bonjour Kossi', $this->view->render('nested'));
    }

    public function testRenderPageWrapsContentInLayout(): void
    {
        $html = $this->view->renderPage('greeting', ['name' => 'Awa'], ['title' => 'Accueil']);

        self::assertStringStartsWith('<main data-title="Accueil"><p class="greeting">Bonjour Awa</p>', $html);
    }

    public function testMissingTemplateThrows(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Gabarit introuvable : absent');

        $this->view->render('absent');
    }

    public function testFailingTemplateDoesNotLeakOutputBuffers(): void
    {
        $level = ob_get_level();

        try {
            $this->view->render('broken');
            self::fail('Une exception était attendue.');
        } catch (RuntimeException $exception) {
            self::assertSame('gabarit cassé', $exception->getMessage());
        }

        self::assertSame($level, ob_get_level());
    }

    public function testAssetUrlCarriesFileVersion(): void
    {
        $file = self::FIXTURES . '/public/assets/css/test.css';

        self::assertSame('/assets/css/test.css?v=' . filemtime($file), $this->view->asset('css/test.css'));
        self::assertSame('/assets/css/absent.css?v=0', $this->view->asset('/css/absent.css'));
    }
}
