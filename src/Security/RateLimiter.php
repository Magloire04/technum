<?php

declare(strict_types=1);

namespace Technum\Security;

use RuntimeException;

/**
 * Limite le nombre de demandes envoyées par connexion. Chaque connexion est reconnue
 * par une empreinte HMAC de son adresse IP : l'adresse elle-même n'est jamais écrite.
 */
final class RateLimiter
{
    public function __construct(
        private readonly string $directory,
        private readonly string $secret,
        private readonly int $maxHits = 5,
        private readonly int $windowSeconds = 3600,
    ) {
    }

    public function isLimited(string $clientIp, int $now): bool
    {
        return count($this->recentHits($this->fileFor($clientIp), $now)) >= $this->maxHits;
    }

    public function hit(string $clientIp, int $now): void
    {
        $file = $this->fileFor($clientIp);
        $hits = [...$this->recentHits($file, $now), $now];
        $this->ensureDirectory();
        file_put_contents($file, implode("\n", $hits), LOCK_EX);
    }

    public function purgeExpired(int $now): void
    {
        foreach (glob($this->directory . '/*.hits') ?: [] as $file) {
            if ($this->recentHits($file, $now) === []) {
                unlink($file);
            }
        }
    }

    private function fileFor(string $clientIp): string
    {
        return $this->directory . '/' . hash_hmac('sha256', 'rate-limit|' . $clientIp, $this->secret) . '.hits';
    }

    /**
     * @return list<int>
     */
    private function recentHits(string $file, int $now): array
    {
        if (!is_file($file)) {
            return [];
        }
        $hits = [];
        foreach (explode("\n", (string) file_get_contents($file)) as $line) {
            if (ctype_digit($line) && (int) $line > $now - $this->windowSeconds) {
                $hits[] = (int) $line;
            }
        }

        return $hits;
    }

    private function ensureDirectory(): void
    {
        if (!is_dir($this->directory) && !mkdir($this->directory, 0750, true) && !is_dir($this->directory)) {
            throw new RuntimeException('Dossier de limitation des envois inaccessible.');
        }
    }
}
