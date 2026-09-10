<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\ActivityCell;
use Atelier\Chart\Model\ActivityChart;
use Atelier\Chart\Model\ActivityLayout;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ActivityChart::class)]
#[UsesClass(ActivityCell::class)]
final class ActivityChartTest extends TestCase
{
    #[Test]
    public function exposesMetadataAndMaximum(): void
    {
        $chart = new ActivityChart(
            'Changes',
            [new ActivityCell('Mon', 0.0), new ActivityCell('Tue', 8.0)],
            ActivityLayout::Calendar,
            'Release activity',
            'Two days.',
            'Eight changes',
            640.0,
            280.0,
        );

        self::assertSame(8.0, $chart->maximum());
        self::assertSame(640.0, $chart->width());
        self::assertSame(280.0, $chart->height());
        self::assertSame('Release activity', $chart->title());
        self::assertSame('Two days.', $chart->description());
    }

    #[Test]
    public function rejectsAnEmptySeriesName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ActivityChart(' ', [new ActivityCell('A', 1.0)], ActivityLayout::Calendar);
    }

    #[Test]
    public function rejectsEmptyCells(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ActivityChart('Changes', [], ActivityLayout::Calendar);
    }

    #[Test]
    public function rejectsDuplicateCategories(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ActivityChart('Changes', [new ActivityCell('A', 1.0), new ActivityCell('A', 2.0)], ActivityLayout::Calendar);
    }

    #[Test]
    public function rejectsAnEmptyTitle(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ActivityChart('Changes', [new ActivityCell('A', 1.0)], ActivityLayout::Calendar, ' ');
    }

    #[Test]
    public function rejectsAnEmptyDescription(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ActivityChart('Changes', [new ActivityCell('A', 1.0)], ActivityLayout::Calendar, chartDescription: ' ');
    }

    #[Test]
    public function rejectsAnEmptySummary(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ActivityChart('Changes', [new ActivityCell('A', 1.0)], ActivityLayout::Calendar, summary: ' ');
    }

    #[Test]
    public function rejectsAnInvalidSize(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ActivityChart('Changes', [new ActivityCell('A', 1.0)], ActivityLayout::Calendar, chartHeight: 100.0);
    }
}
