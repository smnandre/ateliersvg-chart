<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Builder;

use Atelier\Chart\Builder\CartesianChartBuilder;
use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\ChartKind;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CartesianChartBuilder::class)]
final class CartesianChartBuilderTest extends TestCase
{
    #[Test]
    public function buildsAnExplicitlyConfiguredChart(): void
    {
        $chart = (new CartesianChartBuilder(ChartKind::Line))
            ->title('Weekly values')
            ->description('A compact comparison.')
            ->size(640.0, 320.0)
            ->showValues()
            ->smooth()
            ->series('Primary', ['Mon' => 12, 'Tue' => 18], '#123456')
            ->series('Secondary', ['Mon' => 8, 'Tue' => 14])
            ->build();

        self::assertSame(['Mon', 'Tue'], $chart->categories);
        self::assertCount(2, $chart->series);
        self::assertSame([12.0, 18.0], $chart->series[0]->values);
        self::assertSame('#123456', $chart->series[0]->color);
        self::assertTrue($chart->showValues);
        self::assertTrue($chart->smooth);
        self::assertSame(640.0, $chart->width());
    }

    #[Test]
    public function barsShowValuesByDefault(): void
    {
        $chart = (new CartesianChartBuilder(ChartKind::Bar))
            ->series('Values', ['A' => 1])
            ->build();

        self::assertTrue($chart->showValues);
        self::assertSame('Bar chart', $chart->title());
    }

    #[Test]
    public function rejectsMismatchedCategories(): void
    {
        $builder = (new CartesianChartBuilder(ChartKind::Line))
            ->series('A', ['Mon' => 1, 'Tue' => 2]);

        $this->expectException(InvalidArgumentException::class);

        $builder->series('B', ['Mon' => 3, 'Wed' => 4]);
    }

    #[Test]
    public function requiresADataSeries(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new CartesianChartBuilder(ChartKind::Area))->build();
    }

    #[Test]
    public function rejectsSmoothCurvesForBars(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new CartesianChartBuilder(ChartKind::Bar))->smooth();
    }

    #[Test]
    public function exposesAutomaticAndFixedDomainPolicies(): void
    {
        $line = (new CartesianChartBuilder(ChartKind::Line))->series('A', ['A' => 10, 'B' => 20])->build();
        $bar = (new CartesianChartBuilder(ChartKind::Bar))->includeZero(false)->domain(10.0, 20.0)->series('A', ['A' => 12])->build();

        self::assertFalse($line->domain->includeZero);
        self::assertSame(10.0, $bar->domain->minimum);
    }

    #[Test]
    public function rejectsNonNumericRuntimeValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new CartesianChartBuilder(ChartKind::Bar))->series('Bad', ['A' => 'not numeric']);
    }
}
