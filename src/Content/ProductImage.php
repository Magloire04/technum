<?php

declare(strict_types=1);

namespace Technum\Content;

/**
 * Capture d'un produit. srcSmall vaut '' ou désigne la même image en largeur moitié.
 */
final class ProductImage
{
    public function __construct(
        public readonly string $src,
        public readonly string $srcSmall,
        public readonly int $width,
        public readonly int $height,
        public readonly string $alt,
        public readonly string $frame,
    ) {
    }
}
