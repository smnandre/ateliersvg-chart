<?php

declare(strict_types=1);

namespace Atelier\Chart;

use Atelier\Chart\Builder\ActivityChartBuilder;
use Atelier\Chart\Builder\CartesianChartBuilder;
use Atelier\Chart\Builder\DivergingBarChartBuilder;
use Atelier\Chart\Builder\GaugeBuilder;
use Atelier\Chart\Builder\PieChartBuilder;
use Atelier\Chart\Builder\PointChartBuilder;
use Atelier\Chart\Builder\SparklineBuilder;
use Atelier\Chart\Builder\StackedBarChartBuilder;
use Atelier\Chart\Builder\StripChartBuilder;
use Atelier\Chart\Formatter\CompactValueFormatter;
use Atelier\Chart\Formatter\ValueFormatterInterface;
use Atelier\Chart\Model\ChartKind;
use Atelier\Chart\Model\ChartModel;
use Atelier\Chart\Model\PointChartKind;
use Atelier\Chart\Renderer\Svg\SvgRenderer;
use Atelier\Chart\Renderer\Svg\SvgRenderOptions;
use Atelier\Chart\Theme\Theme;
use Atelier\Svg\Document;

final class Chart
{
    private function __construct()
    {
    }

    public static function bar(): CartesianChartBuilder
    {
        return new CartesianChartBuilder(ChartKind::Bar);
    }

    public static function line(): CartesianChartBuilder
    {
        return new CartesianChartBuilder(ChartKind::Line);
    }

    public static function area(): CartesianChartBuilder
    {
        return new CartesianChartBuilder(ChartKind::Area);
    }

    public static function radar(): CartesianChartBuilder
    {
        return new CartesianChartBuilder(ChartKind::Radar);
    }

    public static function pie(): PieChartBuilder
    {
        return new PieChartBuilder(0.0);
    }

    public static function donut(): PieChartBuilder
    {
        return new PieChartBuilder(0.58);
    }

    public static function scatter(): PointChartBuilder
    {
        return new PointChartBuilder(PointChartKind::Scatter);
    }

    public static function bubble(): PointChartBuilder
    {
        return new PointChartBuilder(PointChartKind::Bubble);
    }

    public static function stackedBar(): StackedBarChartBuilder
    {
        return new StackedBarChartBuilder();
    }

    public static function divergingBar(): DivergingBarChartBuilder
    {
        return new DivergingBarChartBuilder();
    }

    public static function activity(): ActivityChartBuilder
    {
        return new ActivityChartBuilder();
    }

    public static function strip(): StripChartBuilder
    {
        return new StripChartBuilder();
    }

    public static function gauge(float $value): GaugeBuilder
    {
        return new GaugeBuilder($value);
    }

    /**
     * @param non-empty-list<int|float> $values
     */
    public static function sparkline(array $values): SparklineBuilder
    {
        return new SparklineBuilder($values);
    }

    public static function render(
        ChartModel $model,
        ?Theme $theme = null,
        ?ValueFormatterInterface $formatter = null,
        ?SvgRenderOptions $options = null,
    ): string {
        return (new SvgRenderer($formatter ?? new CompactValueFormatter(), options: $options ?? new SvgRenderOptions()))->render($model, $theme ?? Theme::default());
    }

    public static function renderDocument(
        ChartModel $model,
        ?Theme $theme = null,
        ?ValueFormatterInterface $formatter = null,
        ?SvgRenderOptions $options = null,
    ): Document {
        return (new SvgRenderer($formatter ?? new CompactValueFormatter(), options: $options ?? new SvgRenderOptions()))->renderToDocument($model, $theme ?? Theme::default());
    }
}
