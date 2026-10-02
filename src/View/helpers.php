<?php

declare(strict_types=1);

/**
 * Échappe une valeur pour l'afficher dans du HTML, y compris dans un attribut.
 */
function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
}
