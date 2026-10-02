<?php

declare(strict_types=1);

namespace Technum\Content;

final class Typography
{
    private const NBSP = "\u{00A0}";

    /**
     * Espaces insécables de la typographie française : avant « : ; ! ? » » et après « « ».
     */
    public static function french(string $text): string
    {
        $text = preg_replace('/[ \x{00A0}]+([:;!?»])/u', self::NBSP . '$1', $text) ?? $text;

        return preg_replace('/«[ \x{00A0}]+/u', '«' . self::NBSP, $text) ?? $text;
    }
}
