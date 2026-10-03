<?php

declare(strict_types=1);

namespace Technum\Maintenance;

use Technum\Security\RateLimiter;

/**
 * Entretien régulier du site : purge des empreintes d'adresse IP, rotation du journal d'erreurs PHP
 * et alerte quand une demande du formulaire n'a pas pu partir.
 */
final class Maintenance
{
    public function __construct(
        private readonly RateLimiter $rateLimiter,
        private readonly LogRotator $logRotator,
        private readonly MailFailureWatch $mailFailureWatch,
        private readonly string $phpErrorLog,
    ) {
    }

    /**
     * Lignes à signaler. La liste est vide quand tout va bien, pour que cron n'envoie aucun e-mail.
     *
     * @return list<string>
     */
    public function run(int $now): array
    {
        $this->rateLimiter->purgeExpired($now);
        $this->logRotator->rotate($this->phpErrorLog);

        $failures = $this->mailFailureWatch->newFailures();
        if ($failures === 0) {
            return [];
        }

        return [
            $failures === 1
                ? "bytechnum.com : une demande du formulaire n'a pas pu partir depuis le dernier contrôle."
                : sprintf("bytechnum.com : %d demandes du formulaire n'ont pas pu partir depuis le dernier contrôle.", $failures),
            'Le visiteur a vu le message qui propose WhatsApp et l\'e-mail. Vérifier les réglages SMTP du fichier .env du serveur.',
        ];
    }
}
