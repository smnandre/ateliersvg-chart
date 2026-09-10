<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Builder;

use Atelier\Chart\Builder\DivergingBarChartBuilder;
use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\BarOrientation;
use Atelier\Chart\Model\DivergingBarChart;
use Atelier\Chart\Model\Series;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DivergingBarChartBuilder::class)]
#[UsesClass(DivergingBarChart::class)]
#[UsesClass(Series::class)]
final class DivergingBarChartBuilderTest extends TestCase
{
    #[Test]
    public function buildsAConfiguredHorizontalComparison(): void
    {
        $chart = (new DivergingBarChartBuilder())
            ->horizontal()
            ->title('Population')
            ->description('Population by age group.')
            ->size(640.0, 360.0)
            ->showValues()
            ->series('Women', ['0-9' => 18, '10-19' => 22])
            ->series('Men', ['0-9' => 20, '10-19' => 19])
            ->build();

        self::assertSame(BarOrientation::Horizontal, $chart->orientation);
        self::assertTrue($chart->showValues);
        self::assertSame('Population', $chart->title());
        self::assertSame(640.0, $chart->width());
    }

    #[Test]
    public function rejectsMismatchedCategories(): void
    {
        $builder = (new DivergingBarChartBuilder())->series('Women', ['0-9' => 18]);

        $this->expectException(InvalidArgumentException::class);

        $builder->series('Men', ['10-19' => 20]);
    }

    #[Test]
    public function requiresTwoSeries(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new DivergingBarChartBuilder())
            ->series('Women', ['0-9' => 18])
            ->build();
    }

    #[Test]
    public function rejectsAThirdSeries(): void
    {
        $builder = (new DivergingBarChartBuilder())
            ->series('Women', ['0-9' => 18])
            ->series('Men', ['0-9' => 20]);

        $this->expectException(InvalidArgumentException::class);

        $builder->series('Other', ['0-9' => 2]);
    }
}
