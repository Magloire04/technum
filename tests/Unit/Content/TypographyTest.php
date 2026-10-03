<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Content;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Technum\Content\Typography;

final class TypographyTest extends TestCase
{
    /** @return iterable<string, array{string, string}> */
    public static function samples(): iterable
    {
        yield 'deux-points' => ['Exemple : texte', "Exemple\u{00A0}: texte"];
        yield 'guillemets' => ['« Vous êtes ici »', "«\u{00A0}Vous êtes ici\u{00A0}»"];
        yield 'point d\'interrogation' => ['Vraiment ?', "Vraiment\u{00A0}?"];
        yield 'espaces multiples' => ['Déjà  : fait', "Déjà\u{00A0}: fait"];
        yield 'adresse web intacte' => ['https://bytechnum.com', 'https://bytechnum.com'];
        yield 'heure intacte' => ['À 10:30', 'À 10:30'];
    }

    #[DataProvider('samples')]
    public function testFrenchSpacingIsApplied(string $input, string $expected): void
    {
        self::assertSame($expected, Typography::french($input));
    }
}
