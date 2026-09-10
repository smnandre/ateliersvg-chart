<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Builder;

use Atelier\Chart\Builder\PieChartBuilder;
use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\PieChart;
use Atelier\Chart\Model\Slice;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PieChartBuilder::class)]
#[UsesClass(PieChart::class)]
#[UsesClass(Slice::class)]
final class PieChartBuilderTest extends TestCase
{
    #[Test]
    public function buildsAConfiguredDonut(): void
    {
        $chart = (new PieChartBuilder(0.58))
            ->title('Traffic')
            ->description('Traffic sources.')
            ->size(640.0, 360.0)
            ->slice('Direct', 45.0, '#123456')
            ->slice('Search', 35.0)
            ->build();

        self::assertTrue($chart->isDonut());
        self::assertSame('Traffic', $chart->title());
        self::assertSame(640.0, $chart->width());
        self::assertSame('#123456', $chart->slices[0]->color);
    }

    #[Test]
    public function requiresASlice(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new PieChartBuilder(0.0))->build();
    }
}
