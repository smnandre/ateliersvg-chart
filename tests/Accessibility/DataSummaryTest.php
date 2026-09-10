<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Accessibility;

use Atelier\Chart\Accessibility\DataSummary;
use Atelier\Chart\Chart;
use Atelier\Chart\Model\CartesianChart;
use Atelier\Chart\Model\Series;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DataSummary::class)]
#[UsesClass(CartesianChart::class)]
#[UsesClass(Series::class)]
final class DataSummaryTest extends TestCase
{
    #[Test]
    public function exposesCategoriesSeriesAndExactValues(): void
    {
        $chart = Chart::line()->series('Visits', ['Mon' => 1.234567, 'Tue' => 2])->build();

        self::assertSame('Chart data. Visits: Mon 1.234567, Tue 2.', DataSummary::for($chart));
    }

    #[Test]
    public function summarizesEveryPublicChartFamily(): void
    {
        $charts = [
            Chart::stackedBar()->series('A', ['One' => 1])->build(),
            Chart::divergingBar()->series('A', ['One' => 1])->series('B', ['One' => 2])->build(),
            Chart::pie()->slice('One', 1)->build(),
            Chart::scatter()->series('A', [['x' => 1, 'y' => 2]])->build(),
            Chart::activity()->series('A', ['One' => 1])->build(),
            Chart::strip()->segment(12, 'Production')->build(),
            Chart::gauge(50)->series('Target', 60)->build(),
            Chart::sparkline([1, 2])->build(),
        ];

        foreach ($charts as $chart) {
            self::assertStringStartsWith('Chart data. ', DataSummary::for($chart));
        }
    }
}
