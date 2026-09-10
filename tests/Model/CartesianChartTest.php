<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\CartesianChart;
use Atelier\Chart\Model\ChartKind;
use Atelier\Chart\Model\Series;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CartesianChart::class)]
final class CartesianChartTest extends TestCase
{
    #[Test]
    public function exposesChartMetadataAndFlattenedValues(): void
    {
        $chart = new CartesianChart(
            ChartKind::Line,
            ['Mon', 'Tue'],
            [new Series('A', [1.0, 2.0]), new Series('B', [3.0, 4.0])],
            'Weekly values',
            'Two series.',
            640.0,
            320.0,
        );

        self::assertSame(640.0, $chart->width());
        self::assertSame(320.0, $chart->height());
        self::assertSame('Weekly values', $chart->title());
        self::assertSame('Two series.', $chart->description());
        self::assertSame([1.0, 2.0, 3.0, 4.0], $chart->values());
    }

    #[Test]
    public function rejectsDuplicateCategories(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CartesianChart(ChartKind::Bar, ['A', 'A'], [new Series('Values', [1.0, 2.0])], 'Duplicate');
    }

    #[Test]
    public function rejectsMismatchedSeriesLength(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CartesianChart(ChartKind::Bar, ['A', 'B'], [new Series('Values', [1.0])], 'Mismatch');
    }

    #[Test]
    public function rejectsAnEmptyTitle(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CartesianChart(ChartKind::Bar, ['A'], [new Series('Values', [1.0])], ' ');
    }

    #[Test]
    public function rejectsAnEmptyDescription(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CartesianChart(ChartKind::Bar, ['A'], [new Series('Values', [1.0])], 'Values', ' ');
    }

    #[Test]
    public function rejectsAChartThatIsTooSmall(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CartesianChart(ChartKind::Bar, ['A'], [new Series('Values', [1.0])], 'Values', chartWidth: 200.0);
    }

    #[Test]
    public function rejectsRadarWithFewerThanThreeCategories(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CartesianChart(ChartKind::Radar, ['A', 'B'], [new Series('Values', [1.0, 2.0])], 'Radar');
    }

    #[Test]
    public function rejectsNegativeRadarValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CartesianChart(ChartKind::Radar, ['A', 'B', 'C'], [new Series('Values', [1.0, -2.0, 3.0])], 'Radar');
    }
}
