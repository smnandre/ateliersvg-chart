<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\StripChart;
use Atelier\Chart\Model\StripSegment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(StripChart::class)]
#[UsesClass(StripSegment::class)]
final class StripChartTest extends TestCase
{
    #[Test]
    public function exposesItsMetadataAndDefaultCanvas(): void
    {
        $chart = new StripChart([new StripSegment(1, 'Production')]);

        self::assertSame('Strip', $chart->title());
        self::assertNull($chart->description());
        self::assertTrue($chart->showsLegend);
        self::assertSame(720.0, $chart->width());
        self::assertSame(240.0, $chart->height());
    }

    #[Test]
    public function totalsAllWeightsWithoutInterpretingLabels(): void
    {
        $chart = new StripChart([new StripSegment(12, 'Any label'), new StripSegment(3, 'Another'), new StripSegment(9, 'Any label')]);
        self::assertSame(24.0, $chart->totalWeight());
        self::assertCount(3, $chart->segments);
    }

    #[Test]
    public function rejectsAWeightTotalThatOverflows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StripChart([new StripSegment(PHP_FLOAT_MAX, 'A'), new StripSegment(PHP_FLOAT_MAX, 'B')]);
    }

    #[Test]
    public function rejectsAnEmptySegmentList(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new StripChart([]);
    }

    #[Test]
    public function rejectsAnEmptyTitle(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new StripChart([new StripSegment(1, 'Production')], ' ');
    }

    #[Test]
    public function rejectsAnEmptyDescription(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new StripChart([new StripSegment(1, 'Production')], 'Strip', ' ');
    }

    #[Test]
    public function rejectsACanvasBelowTheMinimum(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new StripChart([new StripSegment(1, 'Production')], 'Strip', null, true, 300.0, 240.0);
    }

    #[Test]
    public function rejectsACanvasShorterThanTheMinimum(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new StripChart([new StripSegment(1, 'Production')], 'Strip', null, true, 720.0, 120.0);
    }
}
