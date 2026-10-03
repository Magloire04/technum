<?php

declare(strict_types=1);

namespace Technum\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Technum\Application;
use Technum\Config;
use Technum\Contact\FormToken;
use Technum\Http\Request;
use Technum\Http\Response;
use Technum\Tests\Support\FakeMailer;
use Technum\Tests\Support\FixedClock;
use Technum\Tests\Support\TempDirectory;

abstract class ApplicationTestCase extends TestCase
{
    protected const ROOT = __DIR__ . '/../..';

    protected const SECRET = 'kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk';

    protected const NOW = 1_790_000_000;

    protected FakeMailer $mailer;

    protected FixedClock $clock;

    protected string $storageDir;

    protected function setUp(): void
    {
        $this->mailer = new FakeMailer();
        $this->clock = new FixedClock(self::NOW);
        $this->storageDir = TempDirectory::create('technum-storage');
    }

    protected function tearDown(): void
    {
        TempDirectory::remove($this->storageDir);
    }

    /**
     * @param array<string, string> $env
     */
    protected function application(array $env = []): Application
    {
        return Application::create(
            self::ROOT,
            Config::fromArray([...self::localEnv(), ...$env]),
            $this->mailer,
            $this->clock,
            $this->storageDir,
        );
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

    /**
     * @param array<array-key, mixed> $body
     */
    protected function post(string $path, array $body, string $clientIp = '203.0.113.10'): Response
    {
        return $this->application()->handle(new Request('POST', Request::normalizePath($path), [], $body, $clientIp));
    }

    protected function tokenIssuedSecondsAgo(int $seconds): string
    {
        return (new FormToken(self::SECRET))->issue(self::NOW - $seconds);
    }
}
