<?php

declare(strict_types=1);

namespace Technum\Tests\Unit\Content;

use PHPUnit\Framework\TestCase;
use Technum\Content\ProductStage;

final class ProductStageTest extends TestCase
{
    public function testStagesAreOrderedWithLabelsAndPositions(): void
    {
        $stages = ProductStage::ordered();

        self::assertSame(['Conception', 'Pilote', 'Bêta', 'En service'], array_map(static fn (ProductStage $stage): string => $stage->label(), $stages));
        self::assertSame([1, 2, 3, 4], array_map(static fn (ProductStage $stage): int => $stage->position(), $stages));
    }

    public function testStageIsReadFromContentValue(): void
    {
        self::assertSame(ProductStage::Beta, ProductStage::from('beta'));
        self::assertSame(ProductStage::Live, ProductStage::from('en-service'));
    }
}
