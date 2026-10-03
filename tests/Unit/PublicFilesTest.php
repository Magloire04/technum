<?php

declare(strict_types=1);

namespace Technum\Tests\Unit;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Les fichiers publiés avec le dépôt ne citent ni un dossier personnel de la machine de développement,
 * ni les outils de travail internes. Ces noms sont reconnus par leur empreinte SHA-256, pour ne jamais
 * figurer dans le dépôt, pas même ici.
 */
final class PublicFilesTest extends TestCase
{
    private const ROOT = __DIR__ . '/../..';

    private const ROOT_FILES = ['README.md', 'CONTRIBUTING.md', '.env.example', 'deploy.sh', 'composer.json', 'package.json'];

    private const DIRECTORIES = ['docs', '.github', 'src', 'templates', 'content', 'public', 'tools'];

    private const TEXT_EXTENSIONS = ['md', 'php', 'html', 'txt', 'xml', 'css', 'js', 'json', 'yml', 'yaml', 'sh', 'htaccess', 'svg'];

    private const INTERNAL_TOOL_WORD_HASHES = [
        'c857d09db23e6822e3600bc06ad8d58f92ed62bc8efd81c753f77048662cb97d',
        'c70eca6b0f88f44d81a41311647e50fda1ac454ec04ffd442b0eb4743a993131',
        'b225390a8984c8de4206c746772ab5ef2abb0a02bcb043da10f6d41c274d1a02',
        'c247e9808f5e81b86fef7da1aeb81d92b8e7adc18c46ee83b946efa3cb8df673',
        '94a168d2da574b00ec6c787377278465271cf2b97e68af076fc3eee87eba8664',
        '10182ab855ff772753c05b2fea333666b5f312835d32936b6b03e08ef2cbd6d3',
        '60965168ce762e949600281ba6d01fee136e5b6e8257b1f216f9025ed324474c',
        '7d3194f79e645c42e4396dda38be04766810ec6a00d00aced3ffc2a0a1f1a9ef',
        '3ea125d0bff386e6754b3782b300016fc79a9cf8f8669c0a5c3db64467ddb681',
    ];

    /**
     * @return list<string>
     */
    private static function publicTextFiles(): array
    {
        $files = array_map(static fn (string $name): string => self::ROOT . '/' . $name, self::ROOT_FILES);
        foreach (self::DIRECTORIES as $directory) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(self::ROOT . '/' . $directory, FilesystemIterator::SKIP_DOTS));
            foreach ($iterator as $file) {
                if (!$file instanceof SplFileInfo) {
                    continue;
                }
                $extension = $file->getExtension() !== '' ? $file->getExtension() : ltrim($file->getFilename(), '.');
                if (in_array(strtolower($extension), self::TEXT_EXTENSIONS, true)) {
                    $files[] = $file->getPathname();
                }
            }
        }

        return $files;
    }

    public function testNoFileNamesAPersonalFolderOfTheDevelopmentMachine(): void
    {
        foreach (self::publicTextFiles() as $file) {
            $content = str_replace(chr(92), '/', (string) file_get_contents($file));

            self::assertStringNotContainsStringIgnoringCase('/Users/', $content, $file);
        }
    }

    public function testNoFileNamesAnInternalWorkTool(): void
    {
        foreach (self::publicTextFiles() as $file) {
            $words = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower((string) file_get_contents($file)), -1, PREG_SPLIT_NO_EMPTY);
            $hashes = array_map(static fn (string $word): string => hash('sha256', $word), array_unique($words !== false ? $words : []));

            self::assertSame([], array_values(array_intersect(self::INTERNAL_TOOL_WORD_HASHES, $hashes)), $file);
        }
    }

    public function testNoFileShowsBrowserAutomationToolCalls(): void
    {
        foreach (self::publicTextFiles() as $file) {
            self::assertDoesNotMatchRegularExpression('/\bbrowser_[a-z]/', (string) file_get_contents($file), $file);
        }
    }
}
