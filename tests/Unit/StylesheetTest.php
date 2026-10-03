<?php

declare(strict_types=1);

namespace Technum\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class StylesheetTest extends TestCase
{
    private const STYLESHEET = __DIR__ . '/../../public/assets/css/site.css';

    private const SCRIPT = __DIR__ . '/../../public/assets/js/site.js';

    private const MOTION = '@media (prefers-reduced-motion: no-preference) {';

    public function testLoadSequenceRunsOnlyWhenTheVisitorAcceptsMotion(): void
    {
        $css = (string) file_get_contents(self::STYLESHEET);
        [$motion, $rest] = self::splitMotion($css);

        foreach (['bit-in', 'braces-open', 'tile-rise', 'panel-in'] as $animation) {
            self::assertStringContainsString($animation, $motion);
        }
        self::assertStringNotContainsString('animation:', $rest);
        self::assertStringNotContainsString('translateY(-', $rest);
    }

    public function testStylesheetAndScriptStayLight(): void
    {
        self::assertLessThan(40_000, (int) filesize(self::STYLESHEET));
        self::assertLessThan(10_000, (int) filesize(self::SCRIPT));
    }

    /**
     * Sépare le contenu des blocs de mouvement du reste de la feuille de style.
     *
     * @return array{string, string}
     */
    private static function splitMotion(string $css): array
    {
        $motion = '';
        while (($start = strpos($css, self::MOTION)) !== false) {
            $depth = 0;
            $end = $start;
            $length = strlen($css);
            for ($i = $start + strlen(self::MOTION) - 1; $i < $length; ++$i) {
                if ($css[$i] === '{') {
                    ++$depth;
                } elseif ($css[$i] === '}') {
                    --$depth;
                    if ($depth === 0) {
                        $end = $i;
                        break;
                    }
                }
            }
            $motion .= substr($css, $start, $end - $start + 1);
            $css = substr($css, 0, $start) . substr($css, $end + 1);
        }

        return [$motion, $css];
    }
}
