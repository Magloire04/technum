<?php

declare(strict_types=1);

namespace Technum\Security;

use InvalidArgumentException;
use RuntimeException;

/**
 * Journal des refus du formulaire, sans aucune donnée personnelle.
 */
final class SecurityLog
{
    public const MAX_BYTES = 1_000_000;

    public function __construct(private readonly string $file)
    {
    }

    public function record(string $event, int $timestamp): void
    {
        if (preg_match('/^[a-z]+(\.[a-z_]+)+$/', $event) !== 1) {
            throw new InvalidArgumentException("Nom d'événement invalide : minuscules, points et tirets bas uniquement.");
        }
        $directory = dirname($this->file);
        if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
            throw new RuntimeException('Dossier du journal inaccessible.');
        }
        clearstatcache(true, $this->file);
        if (is_file($this->file) && (int) filesize($this->file) > self::MAX_BYTES) {
            rename($this->file, $this->file . '.1');
        }
        file_put_contents($this->file, gmdate('Y-m-d\TH:i:s\Z', $timestamp) . ' ' . $event . "\n", FILE_APPEND | LOCK_EX);
    }
}
