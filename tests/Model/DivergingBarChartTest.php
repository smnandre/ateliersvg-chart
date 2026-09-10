<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\BarOrientation;
use Atelier\Chart\Model\DivergingBarChart;
use Atelier\Chart\Model\Series;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(DivergingBarChart::class)]
final class DivergingBarChartTest extends TestCase
{
    #[Test]
    public function exposesMetadataAndMaximumMagnitude(): void
    {
        $chart = new DivergingBarChart(
            BarOrientation::Vertical,
            ['Jan', 'Feb'],
            [new Series('2024', [21.0, 10.0]), new Series('2023', [13.0, 16.0])],
            'Year comparison',
            'Monthly comparison.',
        );

        self::assertSame(21.0, $chart->maximumMagnitude());
        self::assertSame('Year comparison', $chart->title());
        self::assertSame('Monthly comparison.', $chart->description());
        self::assertSame(720.0, $chart->width());
        self::assertSame(420.0, $chart->height());
    }

    #[Test]
    public function rejectsNegativeMagnitudes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new DivergingBarChart(
            BarOrientation::Vertical,
            ['Jan'],
            [new Series('2024', [21.0]), new Series('2023', [-13.0])],
            'Year comparison',
        );
    }

    #[Test]
    public function rejectsAnEmptyComparison(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new DivergingBarChart(
            BarOrientation::Vertical,
            ['Jan'],
            [new Series('2024', [0.0]), new Series('2023', [0.0])],
            'Year comparison',
        );
    }
}
