<?php

declare(strict_types=1);

namespace Technum\Content;

enum ProductStage: string
{
    case Concept = 'conception';
    case Pilot = 'pilote';
    case Beta = 'beta';
    case Live = 'en-service';

    public function label(): string
    {
        return match ($this) {
            self::Concept => 'Conception',
            self::Pilot => 'Pilote',
            self::Beta => 'Bêta',
            self::Live => 'En service',
        };
    }

    /**
     * Position sur la piste d'état, de 1 à 4.
     */
    public function position(): int
    {
        return match ($this) {
            self::Concept => 1,
            self::Pilot => 2,
            self::Beta => 3,
            self::Live => 4,
        };
    }

    /**
     * @return list<self>
     */
    public static function ordered(): array
    {
        return [self::Concept, self::Pilot, self::Beta, self::Live];
    }
}
