<?php

declare(strict_types=1);

namespace Technum\Maintenance;

/**
 * Garde un journal sous une taille maximale : au-delà, il devient « .1 », qui remplace l'ancien.
 */
final class LogRotator
{
    public function __construct(private readonly int $maxBytes = 1_000_000)
    {
    }

    public function rotate(string $file): bool
    {
        clearstatcache(true, $file);
        if (!is_file($file) || (int) filesize($file) <= $this->maxBytes) {
            return false;
        }

        return rename($file, $file . '.1');
    }
}
