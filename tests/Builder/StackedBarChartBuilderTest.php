<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Builder;

use Atelier\Chart\Builder\StackedBarChartBuilder;
use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\BarOrientation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(StackedBarChartBuilder::class)]
final class StackedBarChartBuilderTest extends TestCase
{
    #[Test]
    public function buildsAConfiguredHorizontalChart(): void
    {
        $chart = (new StackedBarChartBuilder())
            ->horizontal()
            ->title('Allocation')
            ->description('By team.')
            ->size(640.0, 360.0)
            ->showValues()
            ->series('Done', ['Core' => 10, 'Web' => 20], '#123456')
            ->series('Next', ['Core' => 5, 'Web' => 8])
            ->build();

        self::assertSame(BarOrientation::Horizontal, $chart->orientation);
        self::assertSame(['Core', 'Web'], $chart->categories);
        self::assertSame('#123456', $chart->series[0]->color);
        self::assertTrue($chart->showValues);
        self::assertSame(640.0, $chart->width());
    }

    #[Test]
    public function canReturnToVerticalOrientation(): void
    {
        $chart = (new StackedBarChartBuilder())
            ->horizontal()
            ->vertical()
            ->series('One', ['A' => 1])
            ->build();

        self::assertSame(BarOrientation::Vertical, $chart->orientation);
    }

    #[Test]
    public function rejectsMismatchedCategories(): void
    {
        $builder = (new StackedBarChartBuilder())->series('One', ['A' => 1, 'B' => 2]);

        $this->expectException(InvalidArgumentException::class);

        $builder->series('Two', ['A' => 3, 'C' => 4]);
    }

    #[Test]
    public function requiresAtLeastOneSeries(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new StackedBarChartBuilder())->build();
    }
}
