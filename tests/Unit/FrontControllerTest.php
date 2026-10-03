<?php

declare(strict_types=1);

namespace Technum\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class FrontControllerTest extends TestCase
{
    /**
     * Une erreur levée au chargement des dépendances doit finir dans storage/logs, jamais dans le dossier public.
     */
    public function testErrorLogIsConfiguredBeforeDependenciesAreLoaded(): void
    {
        $source = (string) file_get_contents(__DIR__ . '/../../public/index.php');
        $errorLog = strpos($source, "ini_set('error_log'");
        $autoload = strpos($source, 'vendor/autoload.php');

        self::assertNotFalse($errorLog);
        self::assertNotFalse($autoload);
        self::assertLessThan($autoload, $errorLog);
    }
}
