<?php

declare(strict_types=1);

namespace Technum\Clock;

final class SystemClock implements Clock
{
    public function now(): int
    {
        return time();
    }
}
