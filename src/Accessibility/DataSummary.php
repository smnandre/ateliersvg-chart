<?php

declare(strict_types=1);

namespace Atelier\Chart\Accessibility;

use Atelier\Chart\Internal\Number;
use Atelier\Chart\Model\ActivityChart;
use Atelier\Chart\Model\CartesianChart;
use Atelier\Chart\Model\ChartModel;
use Atelier\Chart\Model\DivergingBarChart;
use Atelier\Chart\Model\Gauge;
use Atelier\Chart\Model\PieChart;
use Atelier\Chart\Model\PointChart;
use Atelier\Chart\Model\Series;
use Atelier\Chart\Model\Sparkline;
use Atelier\Chart\Model\StackedBarChart;
use Atelier\Chart\Model\StripChart;

final class DataSummary
{
    private function __construct()
    {
    }

    public static function for(ChartModel $chart): string
    {
        $data = match (true) {
            $chart instanceof CartesianChart => self::series($chart->categories, $chart->series),
            $chart instanceof StackedBarChart => self::series($chart->categories, $chart->series),
            $chart instanceof DivergingBarChart => self::series($chart->categories, $chart->series),
            $chart instanceof PieChart => implode('; ', array_map(
                static fn ($slice): string => $slice->label.': '.self::number($slice->value),
                $chart->slices,
            )),
            $chart instanceof PointChart => implode('; ', array_map(
                static fn ($series): string => $series->name.': '.implode(', ', array_map(
                    static fn ($point): string => sprintf('x %s, y %s, size %s', self::number($point->x), self::number($point->y), self::number($point->size)),
                    $series->points,
                )),
                $chart->series,
            )),
            $chart instanceof ActivityChart => $chart->seriesName.': '.implode(', ', array_map(
                static fn ($cell): string => $cell->category.' '.self::number($cell->value),
                $chart->cells,
            )),
            $chart instanceof StripChart => implode('; ', array_map(
                static fn ($segment): string => $segment->label.': weight '.self::number($segment->weight),
                $chart->segments,
            )),
            $chart instanceof Gauge => implode('; ', array_map(
                static fn ($series): string => $series->name.': '.self::number($series->value).$chart->unit,
                $chart->series(),
            )),
            $chart instanceof Sparkline => implode(', ', array_map(self::number(...), $chart->values)),
            default => 'No structured summary is available',
        };

        return 'Chart data. '.$data.'.';
    }

    /**
     * @param non-empty-list<string> $categories
     * @param non-empty-list<Series> $series
     */
    private static function series(array $categories, array $series): string
    {
        return implode('; ', array_map(
            static fn (Series $item): string => $item->name.': '.implode(', ', array_map(
                static fn (string $category, float $value): string => $category.' '.self::number($value),
                $categories,
                $item->values,
            )),
            $series,
        ));
    }

    private static function number(float $value): string
    {
        return Number::serialize($value);
    }
}
