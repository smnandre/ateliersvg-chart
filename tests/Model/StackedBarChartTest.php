<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\BarOrientation;
use Atelier\Chart\Model\Series;
use Atelier\Chart\Model\StackedBarChart;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(StackedBarChart::class)]
final class StackedBarChartTest extends TestCase
{
    #[Test]
    public function exposesMetadataAndCategoryTotals(): void
    {
        $chart = new StackedBarChart(
            ['A', 'B'],
            [new Series('One', [10.0, 20.0]), new Series('Two', [4.0, 6.0])],
            BarOrientation::Horizontal,
            'Allocation',
            'Two series.',
            640.0,
            320.0,
            true,
        );

        self::assertSame([14.0, 26.0], $chart->totals());
        self::assertSame(640.0, $chart->width());
        self::assertSame(320.0, $chart->height());
        self::assertSame('Allocation', $chart->title());
        self::assertSame('Two series.', $chart->description());
        self::assertTrue($chart->showValues);
    }

    #[Test]
    public function rejectsDuplicateOrEmptyCategories(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new StackedBarChart(['A', 'A'], [new Series('One', [1.0, 2.0])], BarOrientation::Vertical);
    }

    #[Test]
    public function rejectsAnEmptyCategoryLabel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new StackedBarChart([' '], [new Series('One', [1.0])], BarOrientation::Vertical);
    }

    #[Test]
    public function rejectsMismatchedSeries(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new StackedBarChart(['A', 'B'], [new Series('One', [1.0])], BarOrientation::Vertical);
    }

    #[Test]
    public function rejectsNegativeValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new StackedBarChart(['A'], [new Series('One', [-1.0])], BarOrientation::Vertical);
    }

    #[Test]
    public function rejectsEmptyMetadata(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new StackedBarChart(['A'], [new Series('One', [1.0])], BarOrientation::Vertical, chartDescription: ' ');
    }

    #[Test]
    public function rejectsAnInvalidSize(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new StackedBarChart(['A'], [new Series('One', [1.0])], BarOrientation::Vertical, chartHeight: 100.0);
    }
}
