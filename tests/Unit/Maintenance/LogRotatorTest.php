<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Maintenance;

use PHPUnit\Framework\TestCase;
use Technum\Maintenance\LogRotator;
use Technum\Tests\Support\TempDirectory;

final class LogRotatorTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = TempDirectory::create('technum-rotation');
    }

    protected function tearDown(): void
    {
        TempDirectory::remove($this->directory);
    }

    public function testLogLargerThanTheLimitBecomesThePreviousLog(): void
    {
        $file = $this->directory . '/php-errors.log';
        file_put_contents($file, str_repeat('x', 20));
        file_put_contents($file . '.1', 'ancien');

        self::assertTrue((new LogRotator(10))->rotate($file));
        self::assertFileDoesNotExist($file);
        self::assertSame(str_repeat('x', 20), file_get_contents($file . '.1'));
    }

    public function testLogUnderTheLimitStaysInPlace(): void
    {
        $file = $this->directory . '/php-errors.log';
        file_put_contents($file, 'court');

        self::assertFalse((new LogRotator(10))->rotate($file));
        self::assertSame('court', file_get_contents($file));
    }

    public function testMissingLogIsIgnored(): void
    {
        self::assertFalse((new LogRotator(10))->rotate($this->directory . '/absent.log'));
    }
}
