<?php

declare(strict_types=1);

namespace Technum\Maintenance;

/**
 * Compte les demandes du formulaire qui n'ont pas pu partir depuis le dernier passage.
 * Les échecs sont lus dans le journal de sécurité, y compris sa version précédente après une rotation.
 * L'heure du dernier échec déjà signalé est gardée dans un petit fichier d'état.
 */
final class MailFailureWatch
{
    public const EVENT = 'contact.mail_failed';

    public function __construct(
        private readonly string $securityLog,
        private readonly string $stateFile,
    ) {
    }

    public function newFailures(): int
    {
        $lastReported = is_file($this->stateFile) ? trim((string) file_get_contents($this->stateFile)) : '';
        $latest = $lastReported;
        $count = 0;
        foreach ([$this->securityLog . '.1', $this->securityLog] as $file) {
            if (!is_file($file)) {
                continue;
            }
            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
                [$time, $event] = array_pad(explode(' ', $line, 2), 2, '');
                if ($event !== self::EVENT || strcmp($time, $lastReported) <= 0) {
                    continue;
                }
                ++$count;
                if (strcmp($time, $latest) > 0) {
                    $latest = $time;
                }
            }
        }
        if ($latest !== $lastReported) {
            file_put_contents($this->stateFile, $latest . "\n", LOCK_EX);
        }

        return $count;
    }
}
