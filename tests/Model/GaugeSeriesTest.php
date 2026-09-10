<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\GaugeSeries;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(GaugeSeries::class)]
final class GaugeSeriesTest extends TestCase
{
    #[Test]
    public function exposesAValidSeries(): void
    {
        $series = new GaugeSeries('Quality', 84.0, '#123456');

        self::assertSame('Quality', $series->name);
        self::assertSame(84.0, $series->value);
        self::assertSame('#123456', $series->color);
    }

    #[Test]
    public function rejectsAnEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GaugeSeries(' ', 84.0);
    }

    #[Test]
    public function rejectsANonFiniteValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new GaugeSeries('Quality', INF);
    }
}
