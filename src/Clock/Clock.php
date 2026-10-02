<?php

declare(strict_types=1);

namespace Technum\Clock;

interface Clock
{
    /**
     * Horodatage Unix, en secondes.
     */
    public function now(): int;
}
