<?php

declare(strict_types=1);

namespace Technum\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Technum\Application;
use Technum\Config;
use Technum\Http\Request;
use Technum\Http\Response;

abstract class ApplicationTestCase extends TestCase
{
    protected const ROOT = __DIR__ . '/../..';

    protected const SECRET = 'kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk';

    /**
     * @param array<string, string> $env
     */
    protected function application(array $env = []): Application
    {
        return Application::create(self::ROOT, Config::fromArray([...self::localEnv(), ...$env]));
    }

    /**
     * @return array<string, string>
     */
    protected static function localEnv(): array
    {
        return [
            'APP_ENV' => 'local',
            'APP_SECRET' => self::SECRET,
            'MAIL_TRANSPORT' => 'log',
            'CONTACT_SENDER_EMAIL' => 'elisee.atonde@bytechnum.com',
            'CONTACT_RECIPIENT_EMAIL' => 'elisee.atonde@bytechnum.com',
        ];
    }

    /**
     * @param array<string, string> $query
     */
    protected function get(string $path, array $query = [], ?Application $application = null): Response
    {
        $request = new Request('GET', Request::normalizePath($path), $query, [], '203.0.113.10');

        return ($application ?? $this->application())->handle($request);
    }
}
