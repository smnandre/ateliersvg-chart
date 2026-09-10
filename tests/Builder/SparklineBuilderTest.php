<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Builder;

use Atelier\Chart\Builder\SparklineBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(SparklineBuilder::class)]
final class SparklineBuilderTest extends TestCase
{
    #[Test]
    public function buildsAConfiguredSparkline(): void
    {
        $chart = (new SparklineBuilder([1, 3, 2]))
            ->title('Trend')
            ->description('Three values.')
            ->size(180.0, 48.0)
            ->color('#123456')
            ->build();

        self::assertSame([1.0, 3.0, 2.0], $chart->values);
        self::assertSame('Trend', $chart->title());
        self::assertSame('Three values.', $chart->description());
        self::assertSame(180.0, $chart->width());
        self::assertSame('#123456', $chart->color);
    }
}
