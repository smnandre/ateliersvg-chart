<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Builder;

use Atelier\Chart\Builder\GaugeBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(GaugeBuilder::class)]
final class GaugeBuilderTest extends TestCase
{
    #[Test]
    public function buildsAConfiguredGauge(): void
    {
        $gauge = (new GaugeBuilder(72.0))
            ->range(0.0, 120.0)
            ->unit(' ms')
            ->color('#123456')
            ->label('Current')
            ->series('Target', 96.0, '#654321')
            ->title('Latency')
            ->description('Current response time.')
            ->size(480.0, 260.0)
            ->build();

        self::assertSame(72.0, $gauge->value);
        self::assertSame(120.0, $gauge->maximum);
        self::assertSame(' ms', $gauge->unit);
        self::assertSame('#123456', $gauge->color);
        self::assertSame(['Current', 'Target'], array_column($gauge->series(), 'name'));
        self::assertSame('Latency', $gauge->title());
        self::assertSame(480.0, $gauge->width());
    }
}
