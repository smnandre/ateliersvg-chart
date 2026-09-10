<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\Point;
use Atelier\Chart\Model\PointChart;
use Atelier\Chart\Model\PointChartKind;
use Atelier\Chart\Model\PointSeries;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PointChart::class)]
#[UsesClass(Point::class)]
#[UsesClass(PointSeries::class)]
final class PointChartTest extends TestCase
{
    #[Test]
    public function exposesFlattenedCoordinatesAndSizes(): void
    {
        $chart = new PointChart(
            PointChartKind::Bubble,
            [new PointSeries('Markets', [new Point(12.0, 24.0, 80.0), new Point(18.0, 30.0, 120.0)])],
            'Markets',
            xLabel: 'Growth',
            yLabel: 'Margin',
        );

        self::assertSame([12.0, 18.0], $chart->xValues());
        self::assertSame([24.0, 30.0], $chart->yValues());
        self::assertSame([80.0, 120.0], $chart->sizes());
        self::assertSame('Growth', $chart->xLabel);
        self::assertSame('Margin', $chart->yLabel);
        self::assertSame('Markets', $chart->title());
        self::assertNull($chart->description());
        self::assertSame(720.0, $chart->width());
        self::assertSame(420.0, $chart->height());
    }

    #[Test]
    public function rejectsAnEmptyAxisLabel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PointChart(
            PointChartKind::Scatter,
            [new PointSeries('Markets', [new Point(12.0, 24.0)])],
            'Markets',
            xLabel: ' ',
        );
    }

    #[Test]
    public function rejectsAnEmptySeriesCollection(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PointChart(PointChartKind::Scatter, [], 'Markets');
    }

    #[Test]
    public function rejectsAnEmptyTitle(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PointChart(
            PointChartKind::Scatter,
            [new PointSeries('Markets', [new Point(12.0, 24.0)])],
            ' ',
        );
    }

    #[Test]
    public function rejectsAnEmptyDescription(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PointChart(
            PointChartKind::Scatter,
            [new PointSeries('Markets', [new Point(12.0, 24.0)])],
            'Markets',
            ' ',
        );
    }

    #[Test]
    public function rejectsAnEmptyVerticalAxisLabel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PointChart(
            PointChartKind::Scatter,
            [new PointSeries('Markets', [new Point(12.0, 24.0)])],
            'Markets',
            yLabel: ' ',
        );
    }

    #[Test]
    public function rejectsAnInvalidSize(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PointChart(
            PointChartKind::Scatter,
            [new PointSeries('Markets', [new Point(12.0, 24.0)])],
            'Markets',
            chartWidth: 280.0,
        );
    }
}
