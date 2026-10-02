<?php

declare(strict_types=1);

namespace Technum\Content;

final class Service
{
    /**
     * @param list<ServiceExample> $examples
     */
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly array $examples,
    ) {
    }

    public function examplesLabel(): string
    {
        return count($this->examples) > 1 ? 'Exemples' : 'Exemple';
    }
}
