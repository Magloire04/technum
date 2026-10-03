<?php

declare(strict_types=1);

/*
 * Entretien de bytechnum.com, lancé par cron toutes les quinze minutes sur le serveur :
 * purge des empreintes d'adresse IP de plus d'une heure, rotation du journal d'erreurs PHP,
 * alerte quand une demande du formulaire n'a pas pu partir.
 * Le script n'écrit rien quand tout va bien. Cron envoie par e-mail toute sortie : c'est l'alerte.
 */

use Technum\Config;
use Technum\Maintenance\LogRotator;
use Technum\Maintenance\MailFailureWatch;
use Technum\Maintenance\Maintenance;
use Technum\Security\RateLimiter;

$rootDir = dirname(__DIR__);

require $rootDir . '/vendor/autoload.php';

$config = Config::fromEnvFile($rootDir);
$maintenance = new Maintenance(
    new RateLimiter($rootDir . '/storage/rate-limit', $config->appSecret),
    new LogRotator(),
    new MailFailureWatch($rootDir . '/storage/logs/security.log', $rootDir . '/storage/logs/entretien-etat.txt'),
    $rootDir . '/storage/logs/php-errors.log',
);

foreach ($maintenance->run(time()) as $line) {
    echo $line, PHP_EOL;
}
