<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Builder;

use Atelier\Chart\Builder\ActivityChartBuilder;
use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\ActivityCell;
use Atelier\Chart\Model\ActivityChart;
use Atelier\Chart\Model\ActivityLayout;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ActivityChartBuilder::class)]
#[UsesClass(ActivityCell::class)]
#[UsesClass(ActivityChart::class)]
final class ActivityChartBuilderTest extends TestCase
{
    #[Test]
    public function buildsANumericCalendarSeries(): void
    {
        $chart = (new ActivityChartBuilder())
            ->title('Releases')
            ->description('Daily changes.')
            ->summary('17 changes')
            ->size(640.0, 260.0)
            ->strip()
            ->calendar()
            ->series('Changes', ['Mon' => 3, 'Tue' => 8], '#123456')
            ->build();

        self::assertSame(ActivityLayout::Calendar, $chart->layout);
        self::assertSame('Changes', $chart->seriesName);
        self::assertSame('Mon', $chart->cells[0]->category);
        self::assertSame('#123456', $chart->cells[0]->color);
        self::assertSame(640.0, $chart->width());
        self::assertSame('Daily changes.', $chart->description());
        self::assertSame('17 changes', $chart->summary);
    }

    #[Test]
    public function buildsAStyledStripSeries(): void
    {
        $chart = (new ActivityChartBuilder())
            ->strip()
            ->series('Availability', [
                'Yesterday' => ['value' => 1, 'color' => '#22c55e', 'label' => 'Available'],
                'Today' => ['value' => 0, 'color' => '#ef4444', 'label' => 'Unavailable'],
            ])
            ->build();

        self::assertSame(ActivityLayout::Strip, $chart->layout);
        self::assertSame('Unavailable', $chart->cells[1]->label);
        self::assertSame('#ef4444', $chart->cells[1]->color);
    }

    #[Test]
    public function keepsTimelineAsADeprecatedAliasOfStrip(): void
    {
        $chart = (new ActivityChartBuilder())
            ->timeline()
            ->series('Deploys', ['Mon' => 1])
            ->build();

        self::assertSame(ActivityLayout::Strip, $chart->layout);
    }

    #[Test]
    public function requiresOneNonEmptySeries(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ActivityChartBuilder())->build();
    }

    #[Test]
    public function rejectsASecondSeries(): void
    {
        $builder = (new ActivityChartBuilder())->series('One', ['A' => 1]);

        $this->expectException(InvalidArgumentException::class);

        $builder->series('Two', ['A' => 2]);
    }

    #[Test]
    public function rejectsAStyledCellWithoutANumericValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ActivityChartBuilder())->series('Activity', ['Today' => ['color' => '#123456']]);
    }

    #[Test]
    public function rejectsANonStringCellColor(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ActivityChartBuilder())->series('Activity', ['Today' => ['value' => 1, 'color' => 123]]);
    }

    #[Test]
    public function rejectsANonStringCellLabel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ActivityChartBuilder())->series('Activity', ['Today' => ['value' => 1, 'label' => false]]);
    }
}
