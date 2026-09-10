<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests;

use Atelier\Chart\Builder\ActivityChartBuilder;
use Atelier\Chart\Builder\CartesianChartBuilder;
use Atelier\Chart\Builder\DivergingBarChartBuilder;
use Atelier\Chart\Builder\GaugeBuilder;
use Atelier\Chart\Builder\PieChartBuilder;
use Atelier\Chart\Builder\PointChartBuilder;
use Atelier\Chart\Builder\SparklineBuilder;
use Atelier\Chart\Builder\StackedBarChartBuilder;
use Atelier\Chart\Builder\StripChartBuilder;
use Atelier\Chart\Chart;
use Atelier\Chart\Formatter\ValueFormatterInterface;
use Atelier\Chart\Model\CartesianChart;
use Atelier\Chart\Model\ChartKind;
use Atelier\Chart\Model\Sparkline;
use Atelier\Chart\Renderer\Svg\SvgRenderOptions;
use Atelier\Chart\Theme\Theme;
use Atelier\Svg\Document;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Chart::class)]
#[UsesClass(CartesianChartBuilder::class)]
#[UsesClass(DivergingBarChartBuilder::class)]
#[UsesClass(GaugeBuilder::class)]
#[UsesClass(ActivityChartBuilder::class)]
#[UsesClass(PieChartBuilder::class)]
#[UsesClass(PointChartBuilder::class)]
#[UsesClass(SparklineBuilder::class)]
#[UsesClass(StackedBarChartBuilder::class)]
#[UsesClass(StripChartBuilder::class)]
#[UsesClass(CartesianChart::class)]
#[UsesClass(Sparkline::class)]
final class ChartTest extends TestCase
{
    #[Test]
    public function exposesBuildersForEveryInitialFamily(): void
    {
        self::assertInstanceOf(CartesianChartBuilder::class, Chart::bar());
        self::assertInstanceOf(CartesianChartBuilder::class, Chart::line());
        self::assertInstanceOf(CartesianChartBuilder::class, Chart::area());
        self::assertInstanceOf(CartesianChartBuilder::class, Chart::radar());
        self::assertInstanceOf(PieChartBuilder::class, Chart::pie());
        self::assertInstanceOf(PieChartBuilder::class, Chart::donut());
        self::assertInstanceOf(PointChartBuilder::class, Chart::scatter());
        self::assertInstanceOf(PointChartBuilder::class, Chart::bubble());
        self::assertInstanceOf(StackedBarChartBuilder::class, Chart::stackedBar());
        self::assertInstanceOf(DivergingBarChartBuilder::class, Chart::divergingBar());
        self::assertInstanceOf(ActivityChartBuilder::class, Chart::activity());
        self::assertInstanceOf(StripChartBuilder::class, Chart::strip());
        self::assertInstanceOf(GaugeBuilder::class, Chart::gauge(50.0));
        self::assertInstanceOf(SparklineBuilder::class, Chart::sparkline([1, 2]));
    }

    #[Test]
    public function buildersSelectTheirChartKind(): void
    {
        self::assertSame(ChartKind::Bar, Chart::bar()->series('A', ['A' => 1])->build()->kind);
        self::assertSame(ChartKind::Line, Chart::line()->series('A', ['A' => 1])->build()->kind);
        self::assertSame(ChartKind::Area, Chart::area()->series('A', ['A' => 1])->build()->kind);
        self::assertSame(ChartKind::Radar, Chart::radar()->series('A', ['A' => 1, 'B' => 2, 'C' => 3])->build()->kind);
    }

    #[Test]
    public function rendersAChartToSvgAndDocument(): void
    {
        $model = Chart::bar()->series('A', ['One' => 1])->build();

        self::assertStringNotContainsString('data-renderer=', Chart::render($model, Theme::mono()));
        self::assertInstanceOf(Document::class, Chart::renderDocument($model));
    }

    #[Test]
    public function acceptsACustomDisplayFormatter(): void
    {
        $model = Chart::bar()->series('A', ['One' => 1.234567])->build();
        $formatter = new class implements ValueFormatterInterface {
            public function format(float $value): string
            {
                return '$'.number_format($value, 3);
            }
        };

        $svg = Chart::render($model, formatter: $formatter, options: new SvgRenderOptions(dataAttributes: true));

        self::assertStringContainsString('>$1.235</text>', $svg);
        self::assertStringContainsString('data-value="1.234567"', $svg);
    }
}
