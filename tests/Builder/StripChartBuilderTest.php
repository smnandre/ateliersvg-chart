<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Builder;

use Atelier\Chart\Builder\StripChartBuilder;
use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\StripChart;
use Atelier\Chart\Model\StripSegment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(StripChartBuilder::class)]
#[UsesClass(StripChart::class)]
#[UsesClass(StripSegment::class)]
final class StripChartBuilderTest extends TestCase
{
    #[Test]
    public function buildsAStripChartFromNamedStates(): void
    {
        $chart = (new StripChartBuilder())
            ->title('Production')
            ->description('Three weighted segments.')
            ->size(640.0, 220.0)
            ->segment(24, 'Production', '#06bfa8')
            ->segment(weight: 3, label: 'Pause', color: '#f4a34b')
            ->segment(21, 'Production')
            ->build();

        self::assertSame('Production', $chart->title());
        self::assertSame('Three weighted segments.', $chart->description());
        self::assertSame(640.0, $chart->width());
        self::assertSame(220.0, $chart->height());
        self::assertCount(3, $chart->segments);
        self::assertSame(3.0, $chart->segments[1]->weight);
        self::assertSame('#f4a34b', $chart->segments[1]->color);
        self::assertTrue($chart->showsLegend);
    }

    #[Test]
    public function acceptsRepeatedLabelsAcrossSegments(): void
    {
        $chart = (new StripChartBuilder())
            ->segment(3, 'Production')
            ->segment(21, 'Production')
            ->build();

        self::assertSame('Production', $chart->segments[0]->label);
        self::assertSame('Production', $chart->segments[1]->label);
    }

    #[Test]
    public function dropsTheLegendOnRequest(): void
    {
        $chart = (new StripChartBuilder())
            ->withoutLegend()
            ->segment(1, 'Production')
            ->build();

        self::assertFalse($chart->showsLegend);
    }

    #[Test]
    public function requiresAtLeastOneSegment(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new StripChartBuilder())->build();
    }
}
