<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Builder;

use Atelier\Chart\Builder\PointChartBuilder;
use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\Point;
use Atelier\Chart\Model\PointChart;
use Atelier\Chart\Model\PointChartKind;
use Atelier\Chart\Model\PointSeries;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PointChartBuilder::class)]
#[UsesClass(Point::class)]
#[UsesClass(PointChart::class)]
#[UsesClass(PointSeries::class)]
final class PointChartBuilderTest extends TestCase
{
    #[Test]
    public function buildsAConfiguredBubbleChart(): void
    {
        $chart = (new PointChartBuilder(PointChartKind::Bubble))
            ->title('Markets')
            ->description('Growth, margin, and revenue.')
            ->size(640.0, 360.0)
            ->axes('Growth', 'Margin')
            ->series('Enterprise', [
                ['x' => 12, 'y' => 24, 'size' => 80],
                ['x' => 18, 'y' => 30, 'size' => 120],
            ])
            ->build();

        self::assertSame(PointChartKind::Bubble, $chart->kind);
        self::assertSame('Markets', $chart->title());
        self::assertSame(120.0, $chart->series[0]->points[1]->size);
        self::assertSame(640.0, $chart->width());
    }

    #[Test]
    public function requiresBubbleSizes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new PointChartBuilder(PointChartKind::Bubble))->series('Markets', [['x' => 12, 'y' => 24]]);
    }

    #[Test]
    public function requiresASeries(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new PointChartBuilder(PointChartKind::Scatter))->build();
    }

    #[Test]
    public function rejectsMalformedRuntimePoints(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new PointChartBuilder(PointChartKind::Scatter))->series('Bad', [['y' => 24]]);
    }

    #[Test]
    public function configuresIndependentAxisDomains(): void
    {
        $chart = (new PointChartBuilder(PointChartKind::Scatter))
            ->includeZero()
            ->xDomain(1000.0, 1002.0)
            ->yDomain(10.0, 20.0)
            ->series('A', [['x' => 1001, 'y' => 15]])
            ->build();

        self::assertSame(1000.0, $chart->xDomain->minimum);
        self::assertSame(20.0, $chart->yDomain->maximum);
    }
}
