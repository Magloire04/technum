<?php

declare(strict_types=1);

namespace Technum\Tests\Support;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class TempDirectory
{
    public static function create(string $prefix): string
    {
        $directory = sys_get_temp_dir() . '/' . $prefix . '-' . bin2hex(random_bytes(4));
        mkdir($directory, 0777, true);

        return $directory;
    }

    public static function remove(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST,
        );
        foreach ($items as $item) {
            if (!$item instanceof SplFileInfo) {
                continue;
            }
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }
        rmdir($directory);
    }
}
