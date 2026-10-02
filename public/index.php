<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Technum\Application;
use Technum\Config;
use Technum\Http\Request;

$rootDir = dirname(__DIR__);

require $rootDir . '/vendor/autoload.php';

ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', $rootDir . '/storage/logs/php-errors.log');

try {
    $config = Config::fromArray(Dotenv::createArrayBacked($rootDir)->load());
    $application = Application::create($rootDir, $config);
    $request = Request::fromGlobals($_SERVER, $_GET, $_POST, $config->clientIpHeader);
    $application->handle($request)->send();
} catch (Throwable $exception) {
    error_log(sprintf('%s: %s (%s:%d)', $exception::class, $exception->getMessage(), $exception->getFile(), $exception->getLine()));
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
    }
    readfile(__DIR__ . '/500.html');
}
