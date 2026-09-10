<?php

declare(strict_types=1);

namespace Atelier\Chart\Renderer\Svg;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Formatter\CompactValueFormatter;
use Atelier\Chart\Formatter\ValueFormatterInterface;
use Atelier\Chart\Internal\Number;
use Atelier\Chart\Layout\PlotFrame;
use Atelier\Chart\Layout\PlotLayout;
use Atelier\Chart\Model\ActivityCell;
use Atelier\Chart\Model\ActivityChart;
use Atelier\Chart\Model\ActivityLayout;
use Atelier\Chart\Model\BarOrientation;
use Atelier\Chart\Model\CartesianChart;
use Atelier\Chart\Model\ChartKind;
use Atelier\Chart\Model\ChartModel;
use Atelier\Chart\Model\DivergingBarChart;
use Atelier\Chart\Model\Gauge;
use Atelier\Chart\Model\PieChart;
use Atelier\Chart\Model\Point;
use Atelier\Chart\Model\PointChart;
use Atelier\Chart\Model\PointChartKind;
use Atelier\Chart\Model\PointSeries;
use Atelier\Chart\Model\Series;
use Atelier\Chart\Model\Slice;
use Atelier\Chart\Model\Sparkline;
use Atelier\Chart\Model\StackedBarChart;
use Atelier\Chart\Model\StripChart;
use Atelier\Chart\Model\StripSegment;
use Atelier\Chart\Renderer\RendererInterface;
use Atelier\Chart\Scale\LinearScale;
use Atelier\Chart\Theme\Theme;
use Atelier\Svg\Document;
use Atelier\Svg\Dumper\CompactXmlDumper;
use Atelier\Svg\Element\Builder;

final readonly class SvgRenderer implements RendererInterface
{
    public function __construct(
        private ValueFormatterInterface $valueFormatter = new CompactValueFormatter(),
        private PathGeometry $geometry = new PathGeometry(),
        private SvgDocumentFactory $documentFactory = new SvgDocumentFactory(),
        private SvgRenderOptions $options = new SvgRenderOptions(),
    ) {
    }

    public function render(ChartModel $chart, Theme $theme): string
    {
        return (new CompactXmlDumper())->dump($this->renderToDocument($chart, $theme));
    }

    public function renderToDocument(ChartModel $chart, Theme $theme): Document
    {
        $builder = $this->documentFactory->create($chart, $theme, $this->options);

        if ($chart instanceof CartesianChart) {
            if (ChartKind::Radar === $chart->kind) {
                $this->renderRadar($builder, $chart, $theme);
            } else {
                $this->renderCartesian($builder, $chart, $theme);
            }

            return $this->documentFactory->finish($builder);
        }

        if ($chart instanceof StackedBarChart) {
            $this->renderStackedBars($builder, $chart, $theme);

            return $this->documentFactory->finish($builder);
        }

        if ($chart instanceof DivergingBarChart) {
            $this->renderDivergingBars($builder, $chart, $theme);

            return $this->documentFactory->finish($builder);
        }

        if ($chart instanceof ActivityChart) {
            $this->renderActivity($builder, $chart, $theme);

            return $this->documentFactory->finish($builder);
        }

        if ($chart instanceof StripChart) {
            $this->renderStrip($builder, $chart, $theme);

            return $this->documentFactory->finish($builder);
        }

        if ($chart instanceof PieChart) {
            $this->renderPieChart($builder, $chart, $theme);

            return $this->documentFactory->finish($builder);
        }

        if ($chart instanceof PointChart) {
            $this->renderPointChart($builder, $chart, $theme);

            return $this->documentFactory->finish($builder);
        }

        if ($chart instanceof Gauge) {
            $this->renderGauge($builder, $chart, $theme);

            return $this->documentFactory->finish($builder);
        }

        if ($chart instanceof Sparkline) {
            $this->renderSparkline($builder, $chart, $theme);

            return $this->documentFactory->finish($builder);
        }

        throw new InvalidArgumentException(\sprintf('Unsupported chart model %s.', $chart::class));
    }

    private function renderCartesian(Builder $builder, CartesianChart $chart, Theme $theme): void
    {
        $legendRows = count($chart->series) > 1 ? $this->legendRows($chart->series, $chart->width() - 88.0) : 0;
        $frame = (new PlotLayout())->cartesian($chart->width(), $chart->height(), $legendRows);
        $scale = LinearScale::forValues($chart->values(), $frame->plot->bottom(), $frame->plot->y, domain: $chart->domain);

        $this->addText(
            $builder,
            $frame->plot->x,
            $frame->titleBaseline,
            $chart->title(),
            $theme->textColor,
            18.0,
            'start',
            600,
            'atelier-chart__title',
        );

        if (count($chart->series) > 1) {
            $this->renderLegend($builder, $chart->series, $frame, $theme);
        }

        $this->renderAxes($builder, $chart, $frame, $scale, $theme);

        match ($chart->kind) {
            ChartKind::Bar => $this->renderBars($builder, $chart, $frame, $scale, $theme),
            ChartKind::Line => $this->renderLines($builder, $chart, $frame, $scale, $theme, false),
            ChartKind::Area => $this->renderLines($builder, $chart, $frame, $scale, $theme, true),
            ChartKind::Radar => throw new InvalidArgumentException('Radar charts use radial layout.'),
        };
    }

    /**
     * @param non-empty-list<Series> $series
     */
    private function renderLegend(Builder $builder, array $series, PlotFrame $frame, Theme $theme): void
    {
        $x = $frame->plot->x;
        $baseline = $frame->legendBaseline;

        foreach ($series as $index => $dataSeries) {
            $itemWidth = $this->legendItemWidth($dataSeries->name);
            if ($x > $frame->plot->x && $x + $itemWidth > $frame->plot->right()) {
                $x = $frame->plot->x;
                $baseline += 22.0;
            }
            $color = $dataSeries->color ?? $theme->colorAt($index);
            $this->addRect($builder, $x, $baseline - 9.0, 14.0, 4.0, $color, 2.0, 'atelier-chart__legend-mark');
            $this->addText(
                $builder,
                $x + 21.0,
                $baseline,
                $dataSeries->name,
                $theme->mutedTextColor,
                11.0,
                'start',
                500,
                'atelier-chart__legend-label',
            );
            $x += $itemWidth;
        }
    }

    private function renderAxes(
        Builder $builder,
        CartesianChart $chart,
        PlotFrame $frame,
        LinearScale $scale,
        Theme $theme,
    ): void {
        foreach ($scale->ticks() as $tick) {
            $y = $scale->map($tick);
            $isBaseline = abs($tick) < 1e-9;
            $this->addLine(
                $builder,
                $frame->plot->x,
                $y,
                $frame->plot->right(),
                $y,
                $isBaseline ? $theme->axisColor : $theme->gridColor,
                $isBaseline ? 1.25 : 1.0,
                $isBaseline ? 'atelier-chart__baseline' : 'atelier-chart__grid-line',
            );
            $this->addText(
                $builder,
                $frame->plot->x - 11.0,
                $y + 4.0,
                $this->formatValue($tick),
                $theme->mutedTextColor,
                10.5,
                'end',
                400,
                'atelier-chart__tick-label',
            );
        }

        $bandWidth = $frame->plot->width / count($chart->categories);

        foreach ($chart->categories as $index => $category) {
            $this->addText(
                $builder,
                $frame->plot->x + ($index + 0.5) * $bandWidth,
                $frame->categoryBaseline,
                $category,
                $theme->mutedTextColor,
                11.0,
                'middle',
                500,
                'atelier-chart__category-label',
            );
        }
    }

    private function renderBars(
        Builder $builder,
        CartesianChart $chart,
        PlotFrame $frame,
        LinearScale $scale,
        Theme $theme,
    ): void {
        $bandWidth = $frame->plot->width / count($chart->categories);
        $groupWidth = $bandWidth * 0.72;
        $gap = min(6.0, max(2.0, $bandWidth * 0.025));
        $barWidth = max(1.0, ($groupWidth - $gap * (count($chart->series) - 1)) / count($chart->series));
        $baseline = $scale->map(max($scale->domainMin, min($scale->domainMax, 0.0)));

        foreach ($chart->series as $seriesIndex => $series) {
            $color = $series->color ?? $theme->colorAt($seriesIndex);

            foreach ($series->values as $valueIndex => $value) {
                $valueY = $scale->map($value);
                $x = $frame->plot->x
                    + $valueIndex * $bandWidth
                    + ($bandWidth - $groupWidth) / 2.0
                    + $seriesIndex * ($barWidth + $gap);
                $y = min($valueY, $baseline);
                $height = max(0.75, abs($baseline - $valueY));

                $this->addRect(
                    $builder,
                    $x,
                    $y,
                    $barWidth,
                    $height,
                    $color,
                    min(4.0, $barWidth / 4.0),
                    'atelier-chart__bar',
                    $series,
                    $chart->categories[$valueIndex],
                    $value,
                );

                if ($chart->showValues) {
                    $this->addText(
                        $builder,
                        $x + $barWidth / 2.0,
                        $value >= 0.0 ? $y - 8.0 : $y + $height + 15.0,
                        $this->formatValue($value),
                        $theme->textColor,
                        10.5,
                        'middle',
                        600,
                        'atelier-chart__value-label',
                    );
                }
            }
        }
    }

    private function renderLines(
        Builder $builder,
        CartesianChart $chart,
        PlotFrame $frame,
        LinearScale $scale,
        Theme $theme,
        bool $area,
    ): void {
        $bandWidth = $frame->plot->width / count($chart->categories);
        $baseline = $scale->map(max($scale->domainMin, min($scale->domainMax, 0.0)));

        foreach ($chart->series as $seriesIndex => $series) {
            $color = $series->color ?? $theme->colorAt($seriesIndex);
            $points = [];

            foreach ($series->values as $valueIndex => $value) {
                $x = $frame->plot->x + ($valueIndex + 0.5) * $bandWidth;
                $y = $scale->map($value);
                $points[] = [$x, $y, $value];
            }

            $path = $this->geometry->cartesianLine($points, $chart->smooth);

            if ($area) {
                $firstX = $points[0][0];
                $lastX = $points[count($points) - 1][0];
                $areaPath = 'M '.$this->format($firstX).' '.$this->format($baseline)
                    .' L '.substr($path, 2)
                    .' L '.$this->format($lastX).' '.$this->format($baseline).' Z';
                $this->addPath($builder, $areaPath, $color, null, 0.0, 0.14, 'atelier-chart__area');
            }

            $this->addPath($builder, $path, 'none', $color, 3.0, null, 'atelier-chart__line');

            foreach ($points as $pointIndex => [$x, $y, $value]) {
                $this->addCircle(
                    $builder,
                    $x,
                    $y,
                    4.0,
                    $theme->backgroundColor,
                    $color,
                    2.5,
                    'atelier-chart__point',
                    $series,
                    $chart->categories[$pointIndex],
                    $value,
                );

                if ($chart->showValues) {
                    $this->addText(
                        $builder,
                        $x,
                        $y - 11.0,
                        $this->formatValue($value),
                        $theme->textColor,
                        10.5,
                        'middle',
                        600,
                        'atelier-chart__value-label',
                    );
                }
            }
        }
    }

    private function renderRadar(Builder $builder, CartesianChart $chart, Theme $theme): void
    {
        $legendRows = count($chart->series) > 1 ? $this->legendRows($chart->series, $chart->width() - 88.0) : 0;
        $frame = (new PlotLayout())->cartesian($chart->width(), $chart->height(), $legendRows);
        $radarTop = 58.0 + $legendRows * 20.0;
        $radarBottom = $chart->height() - 16.0;
        $centerX = $chart->width() / 2.0;
        $centerY = ($radarTop + $radarBottom) / 2.0;
        $radius = min($chart->width() / 2.0 - 74.0, ($radarBottom - $radarTop) / 2.0 - 18.0);
        if ($radius < 32.0) {
            throw new InvalidArgumentException('The radar chart is too small for its legend and axes. Increase its size or reduce its series.');
        }
        $axisCount = count($chart->categories);
        $scale = LinearScale::forValues($chart->values(), 0.0, $radius, domain: $chart->domain);

        $this->addText(
            $builder,
            $frame->plot->x,
            $frame->titleBaseline,
            $chart->title(),
            $theme->textColor,
            18.0,
            'start',
            600,
            'atelier-chart__title',
        );

        if (count($chart->series) > 1) {
            $this->renderLegend($builder, $chart->series, $frame, $theme);
        }

        for ($level = 1; $level <= 5; ++$level) {
            $gridRadius = $radius * $level / 5.0;
            $this->addPath(
                $builder,
                $this->geometry->radarPolygon($centerX, $centerY, $gridRadius, $axisCount),
                'none',
                $theme->gridColor,
                1.15,
                null,
                'atelier-chart__radar-grid',
            );
            $this->addText(
                $builder,
                $centerX + 5.0,
                $centerY - $gridRadius + 10.0,
                $this->formatValue($scale->domainMax * $level / 5.0),
                $theme->mutedTextColor,
                9.0,
                'start',
                500,
                'atelier-chart__radar-level',
            );
        }

        foreach ($chart->categories as $index => $category) {
            $angle = -M_PI / 2.0 + 2.0 * M_PI * $index / $axisCount;
            $axisX = $centerX + $radius * cos($angle);
            $axisY = $centerY + $radius * sin($angle);
            $labelX = $centerX + ($radius + 18.0) * cos($angle);
            $labelY = $centerY + ($radius + 18.0) * sin($angle) + 4.0;
            $direction = cos($angle);
            $anchor = $direction < -0.2 ? 'end' : ($direction > 0.2 ? 'start' : 'middle');

            $this->addLine(
                $builder,
                $centerX,
                $centerY,
                $axisX,
                $axisY,
                $theme->axisColor,
                1.25,
                'atelier-chart__radar-axis',
            );
            $this->addText(
                $builder,
                $labelX,
                $labelY,
                $category,
                $theme->textColor,
                11.5,
                $anchor,
                600,
                'atelier-chart__radar-label',
            );
        }

        $this->addCircle(
            $builder,
            $centerX,
            $centerY,
            3.0,
            $theme->axisColor,
            $theme->backgroundColor,
            1.5,
            'atelier-chart__radar-center',
        );

        foreach ($chart->series as $seriesIndex => $series) {
            $color = $series->color ?? $theme->colorAt($seriesIndex);
            $path = '';
            $points = [];

            foreach ($series->values as $valueIndex => $value) {
                $angle = -M_PI / 2.0 + 2.0 * M_PI * $valueIndex / $axisCount;
                $pointRadius = $scale->map($value);
                $x = $centerX + $pointRadius * cos($angle);
                $y = $centerY + $pointRadius * sin($angle);
                $points[] = [$x, $y, $value];
                $path .= (0 === $valueIndex ? 'M ' : ' L ').$this->format($x).' '.$this->format($y);
            }

            $path .= ' Z';
            $this->addPath(
                $builder,
                $path,
                'none',
                $color,
                2.75,
                null,
                'atelier-chart__radar-series',
                $series,
                match ($seriesIndex) {
                    0 => null,
                    1 => '8 6',
                    default => '2 5',
                },
            );

            foreach ($points as $pointIndex => [$x, $y, $value]) {
                $this->addCircle(
                    $builder,
                    $x,
                    $y,
                    4.25,
                    $theme->backgroundColor,
                    $color,
                    2.25,
                    'atelier-chart__radar-point',
                    $series,
                    $chart->categories[$pointIndex],
                    $value,
                );

                if ($chart->showValues) {
                    $this->addText(
                        $builder,
                        $x,
                        $y - 9.0,
                        $this->formatValue($value),
                        $theme->textColor,
                        10.0,
                        'middle',
                        600,
                        'atelier-chart__value-label',
                    );
                }
            }
        }
    }

    private function renderActivity(Builder $builder, ActivityChart $chart, Theme $theme): void
    {
        if (ActivityLayout::Strip === $chart->layout) {
            $this->renderActivityStrip($builder, $chart, $theme);

            return;
        }

        $columnCount = (int) ceil(count($chart->cells) / 7);
        $left = 64.0;
        $right = 24.0;
        $top = 78.0;
        $bottom = 44.0;
        $gap = 4.0;
        $cellSize = min(
            20.0,
            ($chart->width() - $left - $right - $gap * ($columnCount - 1)) / $columnCount,
            ($chart->height() - $top - $bottom - $gap * 6.0) / 7.0,
        );
        if ($cellSize < 5.0) {
            throw new InvalidArgumentException('The activity chart is too dense for its dimensions. Increase its size or reduce its cells.');
        }
        $gridWidth = $columnCount * $cellSize + ($columnCount - 1) * $gap;
        $gridX = $left + max(0.0, ($chart->width() - $left - $right - $gridWidth) / 2.0);
        $maximum = $chart->maximum();

        $this->addText($builder, 28.0, 36.0, $chart->title(), $theme->textColor, 18.0, 'start', 600, 'atelier-chart__title');

        foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $row => $day) {
            $this->addText(
                $builder,
                $gridX - 10.0,
                $top + $row * ($cellSize + $gap) + $cellSize / 2.0 + 4.0,
                $day,
                $theme->mutedTextColor,
                9.5,
                'end',
                500,
                'atelier-chart__activity-day',
            );
        }

        $labelStep = max(1, (int) ceil($columnCount / 6));

        foreach ($chart->cells as $index => $cell) {
            $column = intdiv($index, 7);
            $row = $index % 7;
            $ratio = $maximum > 0.0 ? $cell->value / $maximum : 0.0;
            $fill = $cell->color ?? ($cell->value > 0.0 ? $theme->colorAt(0) : $theme->gridColor);
            $opacity = null !== $cell->color || 0.0 === $cell->value ? 1.0 : 0.22 + 0.78 * $ratio;

            $this->addActivityCell(
                $builder,
                $gridX + $column * ($cellSize + $gap),
                $top + $row * ($cellSize + $gap),
                $cellSize,
                $cellSize,
                $fill,
                $opacity,
                min(3.0, $cellSize / 4.0),
                'atelier-chart__activity-cell',
                $chart,
                $cell,
            );

            if (0 === $row && 0 === $column % $labelStep) {
                $this->addText($builder, $gridX + $column * ($cellSize + $gap), $top - 11.0, $cell->category, $theme->mutedTextColor, 9.0, 'start', 500, 'atelier-chart__activity-period');
            }
        }

        $legendY = $chart->height() - 20.0;
        $legendX = $chart->width() - 150.0;
        $this->addText($builder, $legendX - 8.0, $legendY + 3.0, 'Less', $theme->mutedTextColor, 9.0, 'end', 500, 'atelier-chart__activity-legend-label');

        for ($level = 0; $level < 4; ++$level) {
            $fill = 0 === $level ? $theme->gridColor : $theme->colorAt(0);

            $builder->rect($this->round($legendX + $level * 17.0), $this->round($legendY - 8.0), 13.0, 13.0, 2.5);
            $builder->attr('fill', $fill);
            $builder->attr('fill-opacity', $this->format(0 === $level ? 1.0 : 0.22 + 0.26 * $level));
            $this->addClass($builder, 'atelier-chart__activity-legend-step');
            $builder->end();
        }

        $this->addText($builder, $legendX + 72.0, $legendY + 3.0, 'More', $theme->mutedTextColor, 9.0, 'start', 500, 'atelier-chart__activity-legend-label');
    }

    private function renderActivityStrip(Builder $builder, ActivityChart $chart, Theme $theme): void
    {
        $left = 28.0;
        $right = 28.0;
        $top = 82.0;
        $bottom = 66.0;
        $plotWidth = $chart->width() - $left - $right;
        $columnWidth = $plotWidth / count($chart->cells);
        if ($columnWidth < 2.0) {
            throw new InvalidArgumentException('The activity strip is too dense for its width. Increase its width or reduce its cells.');
        }
        $cellHeight = max(24.0, min(56.0, $chart->height() - $top - $bottom));
        $cellGap = min(2.0, $columnWidth * 0.22);
        $maximum = $chart->maximum();
        $lastCell = $chart->cells[array_key_last($chart->cells)];

        $this->addText($builder, 28.0, 36.0, $chart->title(), $theme->textColor, 18.0, 'start', 600, 'atelier-chart__title');

        if (null !== $lastCell->label) {
            $this->addText($builder, $chart->width() - $right, 36.0, $lastCell->label, $lastCell->color ?? $theme->textColor, 13.0, 'end', 600, 'atelier-chart__activity-current');
        }

        foreach ($chart->cells as $index => $cell) {
            $ratio = $maximum > 0.0 ? $cell->value / $maximum : 0.0;
            $fill = $cell->color ?? ($cell->value > 0.0 ? $theme->colorAt(0) : $theme->gridColor);
            $opacity = null !== $cell->color || 0.0 === $cell->value ? 1.0 : 0.22 + 0.78 * $ratio;

            $this->addActivityCell(
                $builder,
                $left + $index * $columnWidth + $cellGap / 2.0,
                $top,
                max(1.0, $columnWidth - $cellGap),
                $cellHeight,
                $fill,
                $opacity,
                min(2.0, $columnWidth / 3.0),
                'atelier-chart__activity-cell atelier-chart__activity-cell--strip',
                $chart,
                $cell,
            );
        }

        $labelY = $top + $cellHeight + 24.0;
        $firstCell = $chart->cells[0];

        $this->addText($builder, $left, $labelY, $firstCell->category, $theme->mutedTextColor, 10.0, 'start', 550, 'atelier-chart__activity-bound');
        $this->addText($builder, $chart->width() - $right, $labelY, $lastCell->category, $theme->mutedTextColor, 10.0, 'end', 550, 'atelier-chart__activity-bound');

        if (null !== $chart->summary) {
            $this->addText($builder, $chart->width() / 2.0, $labelY, $chart->summary, $theme->textColor, 11.0, 'middle', 650, 'atelier-chart__activity-summary');
        }
    }

    private function renderStrip(Builder $builder, StripChart $chart, Theme $theme): void
    {
        $left = 28.0;
        $top = 72.0;
        $plotWidth = $chart->width() - 2.0 * $left;
        $total = $chart->totalWeight();
        $paletteIndices = [];
        $colors = [];
        $legend = [];

        foreach ($chart->segments as $segment) {
            $paletteIndices[$segment->label] ??= count($paletteIndices);
            $color = $segment->color ?? $theme->colorAt($paletteIndices[$segment->label]);
            $colors[] = $color;
            $legendKey = json_encode([$segment->label, $color], JSON_THROW_ON_ERROR);
            $legend[$legendKey] = [$segment->label, $color];
        }

        $columns = max(1, (int) floor($plotWidth / 150.0));
        $columnWidth = $plotWidth / $columns;
        $rows = $chart->showsLegend ? (int) ceil(count($legend) / $columns) : 0;
        $bottom = 28.0 + $rows * 22.0;
        $stripHeight = min(56.0, $chart->height() - $top - $bottom);
        if ($stripHeight < 20.0) {
            throw new InvalidArgumentException('The strip legend is too tall for the chart. Increase its size or hide the legend.');
        }

        $this->addText($builder, $left, 36.0, $chart->title(), $theme->textColor, 18.0, 'start', 600, 'atelier-chart__title');
        $offset = 0.0;
        $lastIndex = array_key_last($chart->segments);

        foreach ($chart->segments as $index => $segment) {
            $start = $left + $plotWidth * ($offset / $total);
            $offset += $segment->weight;
            $end = $index === $lastIndex ? $left + $plotWidth : $left + $plotWidth * ($offset / $total);
            $this->addStripSegment($builder, $start, $top, $end - $start, $stripHeight, $colors[$index], $segment, $segment->weight / $total);
        }

        if (!$chart->showsLegend) {
            return;
        }

        foreach (array_values($legend) as $index => [$label, $color]) {
            if (19.0 + 6.5 * mb_strlen($label) > $columnWidth) {
                throw new InvalidArgumentException('The strip legend label is too wide for the chart. Increase its width or hide the legend.');
            }
            $x = $left + ($index % $columns) * $columnWidth;
            $y = $top + $stripHeight + 28.0 + intdiv($index, $columns) * 22.0;
            $this->addRect($builder, $x, $y - 7.0, 12.0, 4.0, $color, 2.0, 'atelier-chart__strip-legend-mark');
            $this->addText($builder, $x + 19.0, $y, $label, $theme->textColor, 11.0, 'start', 600, 'atelier-chart__strip-legend-label');
        }
    }

    private function renderStackedBars(Builder $builder, StackedBarChart $chart, Theme $theme): void
    {
        $legendRows = count($chart->series) > 1 ? $this->legendRows($chart->series, $chart->width() - 138.0) : 0;
        $frame = BarOrientation::Horizontal === $chart->orientation
            ? (new PlotLayout())->horizontalBar($chart->width(), $chart->height(), $legendRows)
            : (new PlotLayout())->cartesian($chart->width(), $chart->height(), $legendRows);

        $this->addText(
            $builder,
            $frame->plot->x,
            $frame->titleBaseline,
            $chart->title(),
            $theme->textColor,
            18.0,
            'start',
            600,
            'atelier-chart__title',
        );

        if (count($chart->series) > 1) {
            $this->renderLegend($builder, $chart->series, $frame, $theme);
        }

        if (BarOrientation::Horizontal === $chart->orientation) {
            $this->renderHorizontalStackedBars($builder, $chart, $frame, $theme);

            return;
        }

        $this->renderVerticalStackedBars($builder, $chart, $frame, $theme);
    }

    private function renderVerticalStackedBars(
        Builder $builder,
        StackedBarChart $chart,
        PlotFrame $frame,
        Theme $theme,
    ): void {
        $scale = LinearScale::forValues($chart->totals(), $frame->plot->bottom(), $frame->plot->y);
        $bandWidth = $frame->plot->width / count($chart->categories);
        $barWidth = $bandWidth * 0.68;

        foreach ($scale->ticks() as $tick) {
            $y = $scale->map($tick);
            $this->addLine(
                $builder,
                $frame->plot->x,
                $y,
                $frame->plot->right(),
                $y,
                abs($tick) < 1e-9 ? $theme->axisColor : $theme->gridColor,
                abs($tick) < 1e-9 ? 1.25 : 1.0,
                abs($tick) < 1e-9 ? 'atelier-chart__baseline' : 'atelier-chart__grid-line',
            );
            $this->addText(
                $builder,
                $frame->plot->x - 11.0,
                $y + 4.0,
                $this->formatValue($tick),
                $theme->mutedTextColor,
                10.5,
                'end',
                400,
                'atelier-chart__tick-label',
            );
        }

        foreach ($chart->categories as $categoryIndex => $category) {
            $x = $frame->plot->x + $categoryIndex * $bandWidth + ($bandWidth - $barWidth) / 2.0;
            $offset = 0.0;

            foreach ($chart->series as $seriesIndex => $series) {
                $value = $series->values[$categoryIndex];
                $startY = $scale->map($offset);
                $offset += $value;
                $endY = $scale->map($offset);
                $height = max(0.75, $startY - $endY);

                $this->addRect(
                    $builder,
                    $x,
                    $endY,
                    $barWidth,
                    $height,
                    $series->color ?? $theme->colorAt($seriesIndex),
                    0.0,
                    'atelier-chart__stacked-segment',
                    $series,
                    $category,
                    $value,
                );

                if ($chart->showValues && $height >= 20.0) {
                    $this->addText(
                        $builder,
                        $x + $barWidth / 2.0,
                        $endY + $height / 2.0 + 4.0,
                        $this->formatValue($value),
                        $theme->backgroundColor,
                        10.0,
                        'middle',
                        600,
                        'atelier-chart__stacked-value',
                    );
                }
            }

            $this->addText(
                $builder,
                $x + $barWidth / 2.0,
                $frame->categoryBaseline,
                $category,
                $theme->mutedTextColor,
                11.0,
                'middle',
                500,
                'atelier-chart__category-label',
            );
        }
    }

    private function renderHorizontalStackedBars(
        Builder $builder,
        StackedBarChart $chart,
        PlotFrame $frame,
        Theme $theme,
    ): void {
        $scale = LinearScale::forValues($chart->totals(), $frame->plot->x, $frame->plot->right());
        $bandHeight = $frame->plot->height / count($chart->categories);
        $barHeight = $bandHeight * 0.62;

        foreach ($scale->ticks() as $tick) {
            $x = $scale->map($tick);
            $this->addLine(
                $builder,
                $x,
                $frame->plot->y,
                $x,
                $frame->plot->bottom(),
                abs($tick) < 1e-9 ? $theme->axisColor : $theme->gridColor,
                abs($tick) < 1e-9 ? 1.25 : 1.0,
                abs($tick) < 1e-9 ? 'atelier-chart__baseline' : 'atelier-chart__grid-line',
            );
            $this->addText(
                $builder,
                $x,
                $frame->categoryBaseline,
                $this->formatValue($tick),
                $theme->mutedTextColor,
                10.5,
                'middle',
                400,
                'atelier-chart__tick-label',
            );
        }

        foreach ($chart->categories as $categoryIndex => $category) {
            $y = $frame->plot->y + $categoryIndex * $bandHeight + ($bandHeight - $barHeight) / 2.0;
            $offset = 0.0;

            $this->addText(
                $builder,
                $frame->plot->x - 12.0,
                $y + $barHeight / 2.0 + 4.0,
                $category,
                $theme->mutedTextColor,
                11.0,
                'end',
                500,
                'atelier-chart__category-label',
            );

            foreach ($chart->series as $seriesIndex => $series) {
                $value = $series->values[$categoryIndex];
                $startX = $scale->map($offset);
                $offset += $value;
                $endX = $scale->map($offset);
                $width = max(0.75, $endX - $startX);

                $this->addRect(
                    $builder,
                    $startX,
                    $y,
                    $width,
                    $barHeight,
                    $series->color ?? $theme->colorAt($seriesIndex),
                    0.0,
                    'atelier-chart__stacked-segment',
                    $series,
                    $category,
                    $value,
                );

                if ($chart->showValues && $width >= 30.0) {
                    $this->addText(
                        $builder,
                        $startX + $width / 2.0,
                        $y + $barHeight / 2.0 + 4.0,
                        $this->formatValue($value),
                        $theme->backgroundColor,
                        10.0,
                        'middle',
                        600,
                        'atelier-chart__stacked-value',
                    );
                }
            }
        }
    }

    private function renderDivergingBars(Builder $builder, DivergingBarChart $chart, Theme $theme): void
    {
        $legendRows = $this->legendRows($chart->series, $chart->width() - 138.0);
        $frame = BarOrientation::Horizontal === $chart->orientation
            ? (new PlotLayout())->horizontalBar($chart->width(), $chart->height(), $legendRows)
            : (new PlotLayout())->cartesian($chart->width(), $chart->height(), $legendRows);

        $this->addText(
            $builder,
            $frame->plot->x,
            $frame->titleBaseline,
            $chart->title(),
            $theme->textColor,
            18.0,
            'start',
            600,
            'atelier-chart__title',
        );
        $this->renderLegend($builder, $chart->series, $frame, $theme);

        if (BarOrientation::Horizontal === $chart->orientation) {
            $this->renderHorizontalDivergingBars($builder, $chart, $frame, $theme);

            return;
        }

        $this->renderVerticalDivergingBars($builder, $chart, $frame, $theme);
    }

    private function renderVerticalDivergingBars(
        Builder $builder,
        DivergingBarChart $chart,
        PlotFrame $frame,
        Theme $theme,
    ): void {
        $centerY = $frame->plot->y + $frame->plot->height / 2.0;
        $scale = LinearScale::forValues([0.0, $chart->maximumMagnitude()], 0.0, $frame->plot->height / 2.0, 4);

        foreach ($scale->ticks() as $tick) {
            $distance = $scale->map($tick);
            if (abs($tick) < 1e-9) {
                $this->addLine(
                    $builder,
                    $frame->plot->x,
                    $centerY,
                    $frame->plot->right(),
                    $centerY,
                    $theme->axisColor,
                    1.5,
                    'atelier-chart__baseline',
                );
                $this->addText(
                    $builder,
                    $frame->plot->x - 11.0,
                    $centerY + 4.0,
                    '0',
                    $theme->mutedTextColor,
                    10.5,
                    'end',
                    400,
                    'atelier-chart__tick-label',
                );

                continue;
            }

            foreach ([[$centerY - $distance, $tick], [$centerY + $distance, -$tick]] as [$y, $labelValue]) {
                $this->addLine(
                    $builder,
                    $frame->plot->x,
                    $y,
                    $frame->plot->right(),
                    $y,
                    $theme->gridColor,
                    1.0,
                    'atelier-chart__grid-line',
                );
                $this->addText(
                    $builder,
                    $frame->plot->x - 11.0,
                    $y + 4.0,
                    $this->formatValue($labelValue),
                    $theme->mutedTextColor,
                    10.5,
                    'end',
                    400,
                    'atelier-chart__tick-label',
                );
            }
        }

        $bandWidth = $frame->plot->width / count($chart->categories);
        $barWidth = min(28.0, $bandWidth * 0.34);

        foreach ($chart->categories as $categoryIndex => $category) {
            $x = $frame->plot->x + ($categoryIndex + 0.5) * $bandWidth - $barWidth / 2.0;
            $positive = $chart->series[0]->values[$categoryIndex];
            $negative = $chart->series[1]->values[$categoryIndex];
            $positiveHeight = $scale->map($positive);
            $negativeHeight = $scale->map($negative);

            $this->addRect(
                $builder,
                $x,
                $centerY - $positiveHeight,
                $barWidth,
                $positiveHeight,
                $chart->series[0]->color ?? $theme->colorAt(0),
                min(6.0, $barWidth / 2.0),
                'atelier-chart__diverging-bar',
                $chart->series[0],
                $category,
                $positive,
            );
            $this->addRect(
                $builder,
                $x,
                $centerY,
                $barWidth,
                $negativeHeight,
                $chart->series[1]->color ?? $theme->colorAt(1),
                min(6.0, $barWidth / 2.0),
                'atelier-chart__diverging-bar',
                $chart->series[1],
                $category,
                -$negative,
            );

            if ($chart->showValues) {
                $this->addText(
                    $builder,
                    $x + $barWidth / 2.0,
                    $centerY - $positiveHeight - 8.0,
                    $this->formatValue($positive),
                    $theme->textColor,
                    10.0,
                    'middle',
                    600,
                    'atelier-chart__value-label',
                );
                $this->addText(
                    $builder,
                    $x + $barWidth / 2.0,
                    $centerY + $negativeHeight + 15.0,
                    $this->formatValue(-$negative),
                    $theme->textColor,
                    10.0,
                    'middle',
                    600,
                    'atelier-chart__value-label',
                );
            }

            $this->addText(
                $builder,
                $x + $barWidth / 2.0,
                $frame->categoryBaseline,
                $category,
                $theme->mutedTextColor,
                11.0,
                'middle',
                500,
                'atelier-chart__category-label',
            );
        }
    }

    private function renderHorizontalDivergingBars(
        Builder $builder,
        DivergingBarChart $chart,
        PlotFrame $frame,
        Theme $theme,
    ): void {
        $centerX = $frame->plot->x + $frame->plot->width / 2.0;
        $scale = LinearScale::forValues([0.0, $chart->maximumMagnitude()], 0.0, $frame->plot->width / 2.0, 4);

        foreach ($scale->ticks() as $tick) {
            $distance = $scale->map($tick);
            if (abs($tick) < 1e-9) {
                $this->addLine(
                    $builder,
                    $centerX,
                    $frame->plot->y,
                    $centerX,
                    $frame->plot->bottom(),
                    $theme->axisColor,
                    1.5,
                    'atelier-chart__baseline',
                );
                $this->addText(
                    $builder,
                    $centerX,
                    $frame->categoryBaseline,
                    '0',
                    $theme->mutedTextColor,
                    10.5,
                    'middle',
                    400,
                    'atelier-chart__tick-label',
                );

                continue;
            }

            foreach ([[$centerX - $distance, -$tick], [$centerX + $distance, $tick]] as [$x, $labelValue]) {
                $this->addLine(
                    $builder,
                    $x,
                    $frame->plot->y,
                    $x,
                    $frame->plot->bottom(),
                    $theme->gridColor,
                    1.0,
                    'atelier-chart__grid-line',
                );
                $this->addText(
                    $builder,
                    $x,
                    $frame->categoryBaseline,
                    $this->formatValue($labelValue),
                    $theme->mutedTextColor,
                    10.5,
                    'middle',
                    400,
                    'atelier-chart__tick-label',
                );
            }
        }

        $bandHeight = $frame->plot->height / count($chart->categories);
        $barHeight = min(30.0, $bandHeight * 0.48);

        foreach ($chart->categories as $categoryIndex => $category) {
            $y = $frame->plot->y + ($categoryIndex + 0.5) * $bandHeight - $barHeight / 2.0;
            $positive = $chart->series[0]->values[$categoryIndex];
            $negative = $chart->series[1]->values[$categoryIndex];
            $positiveWidth = $scale->map($positive);
            $negativeWidth = $scale->map($negative);

            $this->addRect(
                $builder,
                $centerX,
                $y,
                $positiveWidth,
                $barHeight,
                $chart->series[0]->color ?? $theme->colorAt(0),
                min(6.0, $barHeight / 2.0),
                'atelier-chart__diverging-bar',
                $chart->series[0],
                $category,
                $positive,
            );
            $this->addRect(
                $builder,
                $centerX - $negativeWidth,
                $y,
                $negativeWidth,
                $barHeight,
                $chart->series[1]->color ?? $theme->colorAt(1),
                min(6.0, $barHeight / 2.0),
                'atelier-chart__diverging-bar',
                $chart->series[1],
                $category,
                -$negative,
            );

            if ($chart->showValues) {
                $this->addText(
                    $builder,
                    $centerX + $positiveWidth + 8.0,
                    $y + $barHeight / 2.0 + 4.0,
                    $this->formatValue($positive),
                    $theme->textColor,
                    10.0,
                    'start',
                    600,
                    'atelier-chart__value-label',
                );
                $this->addText(
                    $builder,
                    $centerX - $negativeWidth - 8.0,
                    $y + $barHeight / 2.0 + 4.0,
                    $this->formatValue(-$negative),
                    $theme->textColor,
                    10.0,
                    'end',
                    600,
                    'atelier-chart__value-label',
                );
            }

            $this->addText(
                $builder,
                $frame->plot->x - 12.0,
                $y + $barHeight / 2.0 + 4.0,
                $category,
                $theme->mutedTextColor,
                11.0,
                'end',
                500,
                'atelier-chart__category-label',
            );
        }
    }

    private function renderPieChart(Builder $builder, PieChart $chart, Theme $theme): void
    {
        $this->addText(
            $builder,
            26.0,
            34.0,
            $chart->title(),
            $theme->textColor,
            18.0,
            'start',
            600,
            'atelier-chart__title',
        );

        $wide = $chart->width() >= 520.0;
        $sliceCount = count($chart->slices);

        if ($wide) {
            $centerX = $chart->width() * 0.31;
            $centerY = 70.0 + ($chart->height() - 88.0) / 2.0;
            $radius = min($chart->width() * 0.205, ($chart->height() - 100.0) / 2.0);
            $legendX = $chart->width() * 0.58;
            $legendY = $centerY - ($sliceCount - 1) * 15.0;
            if ($legendY < 66.0 || $legendY + ($sliceCount - 1) * 30.0 > $chart->height() - 18.0) {
                throw new InvalidArgumentException('The pie chart has too many slices for its height. Increase its height or reduce its slices.');
            }
        } else {
            $legendRows = (int) ceil($sliceCount / 2.0);
            $availableHeight = $chart->height() - 94.0 - $legendRows * 24.0;
            $radius = max(34.0, min($chart->width() * 0.27, $availableHeight / 2.0));
            $centerX = $chart->width() / 2.0;
            $centerY = 62.0 + $radius;
            $legendX = 24.0;
            $legendY = $centerY + $radius + 26.0;
            if ($legendY + max(0, $legendRows - 1) * 24.0 > $chart->height() - 18.0) {
                throw new InvalidArgumentException('The pie chart has too many slices for its dimensions. Increase its size or reduce its slices.');
            }
        }

        $innerRadius = $radius * $chart->innerRadiusRatio;
        $angle = -M_PI / 2.0;
        $total = $chart->total();

        foreach ($chart->slices as $slice) {
            $availableLabelWidth = $wide ? 145.0 : $chart->width() / 2.0 - 55.0;
            if (6.5 * mb_strlen($slice->label) > $availableLabelWidth) {
                throw new InvalidArgumentException(sprintf('Pie legend label "%s" is too wide for the chart.', $slice->label));
            }
        }

        foreach ($chart->slices as $sliceIndex => $slice) {
            $ratio = $slice->value / $total;
            $endAngle = $angle + 2.0 * M_PI * $ratio;
            $color = $slice->color ?? $theme->colorAt($sliceIndex);

            $this->addSlicePath(
                $builder,
                $this->geometry->pieSlice($centerX, $centerY, $radius, $innerRadius, $angle, $endAngle),
                $color,
                $theme->backgroundColor,
                $slice,
                $ratio,
            );

            if ($wide) {
                $itemX = $legendX;
                $itemY = $legendY + $sliceIndex * 30.0;
            } else {
                $itemX = $legendX + ($sliceIndex % 2) * ($chart->width() / 2.0 - 12.0);
                $itemY = $legendY + intdiv($sliceIndex, 2) * 24.0;
            }

            $this->addRect($builder, $itemX, $itemY - 7.0, 12.0, 4.0, $color, 2.0, 'atelier-chart__pie-legend-mark');
            $this->addText(
                $builder,
                $itemX + 19.0,
                $itemY,
                $slice->label,
                $theme->textColor,
                11.0,
                'start',
                600,
                'atelier-chart__pie-label',
            );
            $this->addText(
                $builder,
                $itemX + ($wide ? 176.0 : $chart->width() / 2.0 - 31.0),
                $itemY,
                $this->compactNumber($ratio * 100.0).'%',
                $theme->mutedTextColor,
                10.5,
                'end',
                500,
                'atelier-chart__pie-value',
            );

            $angle = $endAngle;
        }

        if ($chart->isDonut()) {
            $this->addText(
                $builder,
                $centerX,
                $centerY - 2.0,
                $this->formatValue($total),
                $theme->textColor,
                25.0,
                'middle',
                650,
                'atelier-chart__donut-total',
            );
            $this->addText(
                $builder,
                $centerX,
                $centerY + 16.0,
                'total',
                $theme->mutedTextColor,
                10.0,
                'middle',
                500,
                'atelier-chart__donut-total-label',
            );
        }
    }

    private function renderPointChart(Builder $builder, PointChart $chart, Theme $theme): void
    {
        $legendRows = count($chart->series) > 1 ? $this->legendRows($chart->series, $chart->width() - 88.0) : 0;
        $frame = (new PlotLayout())->cartesian($chart->width(), $chart->height(), $legendRows);
        $xScale = LinearScale::forValues($chart->xValues(), $frame->plot->x, $frame->plot->right(), domain: $chart->xDomain);
        $yScale = LinearScale::forValues($chart->yValues(), $frame->plot->bottom(), $frame->plot->y, domain: $chart->yDomain);

        $this->addText(
            $builder,
            $frame->plot->x,
            $frame->titleBaseline,
            $chart->title(),
            $theme->textColor,
            18.0,
            'start',
            600,
            'atelier-chart__title',
        );

        if (count($chart->series) > 1) {
            $this->renderPointLegend($builder, $chart->series, $frame, $theme);
        }

        foreach ($yScale->ticks() as $tick) {
            $y = $yScale->map($tick);
            $baseline = abs($tick) < 1e-9;
            $this->addLine(
                $builder,
                $frame->plot->x,
                $y,
                $frame->plot->right(),
                $y,
                $baseline ? $theme->axisColor : $theme->gridColor,
                $baseline ? 1.25 : 1.0,
                $baseline ? 'atelier-chart__baseline' : 'atelier-chart__grid-line',
            );
            $this->addText(
                $builder,
                $frame->plot->x - 11.0,
                $y + 4.0,
                $this->formatValue($tick),
                $theme->mutedTextColor,
                10.5,
                'end',
                400,
                'atelier-chart__tick-label',
            );
        }

        foreach ($xScale->ticks() as $tick) {
            $x = $xScale->map($tick);
            $baseline = abs($tick) < 1e-9;
            $this->addLine(
                $builder,
                $x,
                $frame->plot->y,
                $x,
                $frame->plot->bottom(),
                $baseline ? $theme->axisColor : $theme->gridColor,
                $baseline ? 1.25 : 1.0,
                $baseline ? 'atelier-chart__baseline' : 'atelier-chart__grid-line',
            );
            $this->addText(
                $builder,
                $x,
                $frame->plot->bottom() + 24.0,
                $this->formatValue($tick),
                $theme->mutedTextColor,
                10.5,
                'middle',
                400,
                'atelier-chart__tick-label',
            );
        }

        if (null !== $chart->yLabel) {
            $this->addText(
                $builder,
                $frame->plot->x,
                $frame->plot->y - 10.0,
                $chart->yLabel,
                $theme->mutedTextColor,
                10.5,
                'start',
                600,
                'atelier-chart__axis-label',
            );
        }
        if (null !== $chart->xLabel) {
            $this->addText(
                $builder,
                $frame->plot->right(),
                $chart->height() - 8.0,
                $chart->xLabel,
                $theme->mutedTextColor,
                10.5,
                'end',
                600,
                'atelier-chart__axis-label',
            );
        }

        $maximumSize = max($chart->sizes());

        foreach ($chart->series as $seriesIndex => $series) {
            $color = $series->color ?? $theme->colorAt($seriesIndex);

            foreach ($series->points as $point) {
                $radius = PointChartKind::Bubble === $chart->kind
                    ? $this->geometry->bubbleRadius($point->size, $maximumSize)
                    : 5.5;
                $this->addPointMark(
                    $builder,
                    $xScale->map($point->x),
                    $yScale->map($point->y),
                    $radius,
                    $color,
                    $theme,
                    $series,
                    $point,
                    PointChartKind::Bubble === $chart->kind,
                );
            }
        }
    }

    /**
     * @param non-empty-list<PointSeries> $series
     */
    private function renderPointLegend(Builder $builder, array $series, PlotFrame $frame, Theme $theme): void
    {
        $x = $frame->plot->x;
        $baseline = $frame->legendBaseline;

        foreach ($series as $seriesIndex => $pointSeries) {
            $itemWidth = $this->legendItemWidth($pointSeries->name);
            if ($x > $frame->plot->x && $x + $itemWidth > $frame->plot->right()) {
                $x = $frame->plot->x;
                $baseline += 22.0;
            }
            $color = $pointSeries->color ?? $theme->colorAt($seriesIndex);
            $this->addCircle($builder, $x + 5.0, $baseline - 6.0, 4.0, $color, $color, 0.0, 'atelier-chart__legend-mark');
            $this->addText(
                $builder,
                $x + 16.0,
                $baseline,
                $pointSeries->name,
                $theme->mutedTextColor,
                11.0,
                'start',
                500,
                'atelier-chart__legend-label',
            );
            $x += $itemWidth;
        }
    }

    private function renderGauge(Builder $builder, Gauge $chart, Theme $theme): void
    {
        $centerX = $chart->width() / 2.0;
        $centerY = $chart->height() - 38.0;
        $series = $chart->series();
        $seriesCount = count($series);
        $radius = max(32.0, min($chart->width() * 0.32, $chart->height() - ($seriesCount > 1 ? 116.0 : 100.0)));
        $strokeWidth = 1 === $seriesCount ? min(22.0, max(14.0, $radius * 0.15)) : min(16.0, max(12.0, $radius * 0.11));
        $radiusStep = $strokeWidth + 9.0;

        if (1 < $seriesCount) {
            $columnWidth = ($chart->width() - 52.0) / $seriesCount;
            foreach ($series as $gaugeSeries) {
                $label = $gaugeSeries->name.' '.$this->formatValue($gaugeSeries->value).$chart->unit;
                if (18.0 + 6.5 * mb_strlen($label) > $columnWidth) {
                    throw new InvalidArgumentException('The gauge legend is too wide for the chart. Increase its width or shorten its labels.');
                }
            }
        }

        $this->addText(
            $builder,
            26.0,
            34.0,
            $chart->title(),
            $theme->textColor,
            18.0,
            'start',
            600,
            'atelier-chart__title',
        );
        foreach ($series as $seriesIndex => $gaugeSeries) {
            $seriesRadius = $radius - $seriesIndex * $radiusStep;
            $color = $gaugeSeries->color ?? $theme->colorAt($seriesIndex);
            $ratio = $chart->ratioFor($gaugeSeries);
            $this->addPath(
                $builder,
                $this->geometry->gaugeArc($centerX, $centerY, $seriesRadius, 1.0),
                'none',
                $theme->gridColor,
                $strokeWidth,
                null,
                'atelier-chart__gauge-track',
            );

            if ($ratio > 0.0) {
                $this->addPath(
                    $builder,
                    $this->geometry->gaugeArc($centerX, $centerY, $seriesRadius, $ratio),
                    'none',
                    $color,
                    $strokeWidth,
                    null,
                    'atelier-chart__gauge-value',
                );
            }

            if (1 < $seriesCount) {
                $legendX = 26.0 + $seriesIndex * (($chart->width() - 52.0) / $seriesCount);
                $this->addRect(
                    $builder,
                    $legendX,
                    53.0,
                    12.0,
                    4.0,
                    $color,
                    2.0,
                    'atelier-chart__gauge-legend-mark',
                );
                $this->addText(
                    $builder,
                    $legendX + 18.0,
                    60.0,
                    $gaugeSeries->name.' '.$this->formatValue($gaugeSeries->value).$chart->unit,
                    $theme->mutedTextColor,
                    10.5,
                    'start',
                    600,
                    'atelier-chart__gauge-series-label',
                );
            }
        }

        $this->addText(
            $builder,
            $centerX,
            $centerY - (1 === $seriesCount ? 18.0 : 12.0),
            $this->formatValue($chart->value).$chart->unit,
            $theme->textColor,
            28.0,
            'middle',
            650,
            'atelier-chart__gauge-label',
        );
        $this->addText(
            $builder,
            $centerX - $radius,
            $centerY + 27.0,
            $this->formatValue($chart->minimum),
            $theme->mutedTextColor,
            10.5,
            'start',
            500,
            'atelier-chart__gauge-bound',
        );
        $this->addText(
            $builder,
            $centerX + $radius,
            $centerY + 27.0,
            $this->formatValue($chart->maximum),
            $theme->mutedTextColor,
            10.5,
            'end',
            500,
            'atelier-chart__gauge-bound',
        );
    }

    private function renderSparkline(Builder $builder, Sparkline $chart, Theme $theme): void
    {
        $left = 8.0;
        $right = $chart->width() - 8.0;
        $labelsVisible = $chart->height() >= 48.0;
        $top = $labelsVisible ? 7.0 : 4.0;
        $bottom = $chart->height() - ($labelsVisible ? 18.0 : 4.0);
        $scale = LinearScale::forValues($chart->values, $bottom, $top, 4, domain: \Atelier\Chart\Scale\Domain::automatic());
        $step = ($right - $left) / (count($chart->values) - 1);
        $color = $chart->color ?? $theme->colorAt(0);
        $path = '';

        foreach ($chart->values as $index => $value) {
            $x = $left + $index * $step;
            $y = $scale->map($value);
            $path .= (0 === $index ? 'M ' : ' L ').$this->format($x).' '.$this->format($y);
        }

        $mean = array_sum($chart->values) / count($chart->values);
        $this->addLine($builder, $left, $scale->map($mean), $right, $scale->map($mean), $theme->gridColor, 1.0, 'atelier-chart__sparkline-guide');
        $areaPath = 'M '.$this->format($left).' '.$this->format($bottom)
            .' L '.substr($path, 2)
            .' L '.$this->format($right).' '.$this->format($bottom).' Z';
        $this->addPath($builder, $areaPath, $color, null, 0.0, 0.12, 'atelier-chart__sparkline-area');
        $this->addPath($builder, $path, 'none', $color, 2.5, null, 'atelier-chart__sparkline');
        $this->addCircle(
            $builder,
            $left,
            $scale->map($chart->values[0]),
            2.75,
            $theme->backgroundColor,
            $color,
            2.0,
            'atelier-chart__sparkline-start',
        );
        $lastIndex = count($chart->values) - 1;
        $this->addCircle(
            $builder,
            $right,
            $scale->map($chart->values[$lastIndex]),
            3.5,
            $theme->backgroundColor,
            $color,
            2.0,
            'atelier-chart__sparkline-end',
        );

        if ($labelsVisible) {
            $this->addText(
                $builder,
                $left,
                $chart->height() - 4.0,
                $this->formatValue($chart->values[0]),
                $theme->mutedTextColor,
                9.0,
                'start',
                500,
                'atelier-chart__sparkline-value',
            );
            $this->addText(
                $builder,
                $right,
                $chart->height() - 4.0,
                $this->formatValue($chart->values[$lastIndex]),
                $theme->textColor,
                9.0,
                'end',
                650,
                'atelier-chart__sparkline-value',
            );
        }
    }

    private function addSlicePath(
        Builder $builder,
        string $data,
        string $fill,
        string $stroke,
        Slice $slice,
        float $ratio,
    ): void {
        $builder->path();
        $builder->attr('d', $data);
        $builder->attr('fill', $fill);
        $builder->attr('stroke', $stroke);
        $builder->attr('stroke-width', '2');
        $builder->attr('stroke-linejoin', 'round');
        $this->addClass($builder, 'atelier-chart__pie-slice');
        $this->addData($builder, 'data-label', $slice->label);
        $this->addData($builder, 'data-value', $this->serializeValue($slice->value));
        $this->addData($builder, 'data-ratio', $this->serializeValue($ratio));
        $builder->end();
    }

    private function addPointMark(
        Builder $builder,
        float $x,
        float $y,
        float $radius,
        string $color,
        Theme $theme,
        PointSeries $series,
        Point $point,
        bool $bubble,
    ): void {
        $builder->circle($this->round($x), $this->round($y), $this->round($radius));
        $builder->attr('fill', $color);
        $builder->attr('fill-opacity', $bubble ? '0.55' : '0.9');
        $builder->attr('stroke', $bubble ? $color : $theme->backgroundColor);
        $builder->attr('stroke-width', $bubble ? '2' : '2.5');
        $this->addClass($builder, $bubble ? 'atelier-chart__bubble-point' : 'atelier-chart__plot-point');
        $this->addData($builder, 'data-series', $series->name);
        $this->addData($builder, 'data-x', $this->serializeValue($point->x));
        $this->addData($builder, 'data-y', $this->serializeValue($point->y));
        $this->addData($builder, 'data-size', $this->serializeValue($point->size));
        $builder->end();
    }

    private function addRect(
        Builder $builder,
        float $x,
        float $y,
        float $width,
        float $height,
        string $fill,
        float $radius,
        string $class,
        ?Series $series = null,
        ?string $category = null,
        ?float $value = null,
    ): void {
        $builder->rect($this->round($x), $this->round($y), $this->round($width), $this->round($height), $this->round($radius));
        $builder->attr('fill', $fill);
        $this->addClass($builder, $class);
        $this->addDatumAttributes($builder, $series, $category, $value);
        $builder->end();
    }

    private function addCircle(
        Builder $builder,
        float $cx,
        float $cy,
        float $radius,
        string $fill,
        string $stroke,
        float $strokeWidth,
        string $class,
        ?Series $series = null,
        ?string $category = null,
        ?float $value = null,
    ): void {
        $builder->circle($this->round($cx), $this->round($cy), $this->round($radius));
        $builder->attr('fill', $fill);
        $builder->attr('stroke', $stroke);
        $builder->attr('stroke-width', $this->format($strokeWidth));
        $this->addClass($builder, $class);
        $this->addDatumAttributes($builder, $series, $category, $value);
        $builder->end();
    }

    private function addLine(
        Builder $builder,
        float $x1,
        float $y1,
        float $x2,
        float $y2,
        string $stroke,
        float $strokeWidth,
        string $class,
    ): void {
        $builder->line($this->round($x1), $this->round($y1), $this->round($x2), $this->round($y2));
        $builder->attr('stroke', $stroke);
        $builder->attr('stroke-width', $this->format($strokeWidth));
        $this->addClass($builder, $class);
        $builder->end();
    }

    private function addPath(
        Builder $builder,
        string $data,
        string $fill,
        ?string $stroke,
        float $strokeWidth,
        ?float $opacity,
        string $class,
        ?Series $series = null,
        ?string $dashPattern = null,
    ): void {
        $builder->path();
        $builder->attr('d', $data);
        $builder->attr('fill', $fill);
        $this->addClass($builder, $class);
        if (null !== $stroke) {
            $builder->attr('stroke', $stroke);
            $builder->attr('stroke-width', $this->format($strokeWidth));
            $builder->attr('stroke-linecap', 'round');
            $builder->attr('stroke-linejoin', 'round');
        }
        if (null !== $opacity) {
            $builder->attr('opacity', $this->format($opacity));
        }
        if (null !== $dashPattern) {
            $builder->attr('stroke-dasharray', $dashPattern);
        }
        $this->addDatumAttributes($builder, $series, null, null);
        $builder->end();
    }

    private function addActivityCell(
        Builder $builder,
        float $x,
        float $y,
        float $width,
        float $height,
        string $fill,
        float $opacity,
        float $radius,
        string $class,
        ActivityChart $chart,
        ActivityCell $cell,
    ): void {
        $builder->rect($this->round($x), $this->round($y), $this->round($width), $this->round($height), $this->round($radius));
        $builder->attr('fill', $fill);
        $builder->attr('fill-opacity', $this->format($opacity));
        $this->addClass($builder, $class);
        $this->addData($builder, 'data-series', $chart->seriesName);
        $this->addData($builder, 'data-category', $cell->category);
        $this->addData($builder, 'data-value', $this->serializeValue($cell->value));
        if (null !== $cell->label) {
            $this->addData($builder, 'data-label', $cell->label);
        }
        $builder->end();
    }

    private function addStripSegment(
        Builder $builder,
        float $x,
        float $y,
        float $width,
        float $height,
        string $fill,
        StripSegment $segment,
        float $ratio,
    ): void {
        $builder->rect($x, $y, $width, $height, 0.0);
        $builder->attr('fill', $fill);
        $this->addClass($builder, 'atelier-chart__strip-segment');
        $this->addData($builder, 'data-label', $segment->label);
        $this->addData($builder, 'data-weight', $this->serializeValue($segment->weight));
        $this->addData($builder, 'data-ratio', $this->serializeValue($ratio));
        $builder->end();
    }

    private function addText(
        Builder $builder,
        float $x,
        float $y,
        string $text,
        string $fill,
        float $fontSize,
        string $anchor,
        int $fontWeight,
        string $class,
    ): void {
        $builder->text($this->round($x), $this->round($y), $text);
        $builder->attr('fill', $fill);
        $builder->attr('font-size', $this->format($fontSize));
        $builder->attr('font-weight', (string) $fontWeight);
        $builder->attr('text-anchor', $anchor);
        $this->addClass($builder, $class);
        $builder->end();
    }

    private function addClass(Builder $builder, string $class): void
    {
        if ($this->options->classes) {
            $builder->attr('class', $class);
        }
    }

    private function addData(Builder $builder, string $name, string $value): void
    {
        if ($this->options->dataAttributes) {
            $builder->attr($name, $value);
        }
    }

    private function addDatumAttributes(
        Builder $builder,
        ?Series $series,
        ?string $category,
        ?float $value,
    ): void {
        if (null !== $series) {
            $this->addData($builder, 'data-series', $series->name);
        }
        if (null !== $category) {
            $this->addData($builder, 'data-category', $category);
        }
        if (null !== $value) {
            $this->addData($builder, 'data-value', $this->serializeValue($value));
        }
    }

    private function formatValue(float $value): string
    {
        return $this->valueFormatter->format($value);
    }

    private function compactNumber(float $value): string
    {
        if (abs($value - round($value)) < 1e-9) {
            return number_format($value, 0, '.', '');
        }

        return rtrim(rtrim(number_format($value, 1, '.', ''), '0'), '.');
    }

    /**
     * @param non-empty-list<Series|PointSeries> $series
     */
    private function legendRows(array $series, float $availableWidth): int
    {
        $rows = 1;
        $used = 0.0;

        foreach ($series as $item) {
            $width = $this->legendItemWidth($item->name);
            if ($width > $availableWidth) {
                throw new InvalidArgumentException(sprintf('Legend label "%s" is too wide for the chart.', $item->name));
            }
            if ($used > 0.0 && $used + $width > $availableWidth) {
                ++$rows;
                $used = 0.0;
            }
            $used += $width;
        }

        return $rows;
    }

    private function legendItemWidth(string $label): float
    {
        return 35.0 + max(36.0, 6.5 * mb_strlen($label));
    }

    private function round(float $value): float
    {
        // Normalize arithmetic noise before rounding coordinates across PHP versions.
        return round(round($value, 10), 2);
    }

    private function format(float $value): string
    {
        return rtrim(rtrim(number_format($this->round($value), 2, '.', ''), '0'), '.');
    }

    private function serializeValue(float $value): string
    {
        return Number::serialize($value);
    }
}
