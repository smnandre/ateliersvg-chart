<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\Gauge;
use Atelier\Chart\Model\GaugeSeries;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Gauge::class)]
final class GaugeTest extends TestCase
{
    #[Test]
    public function exposesMetadataAndNormalizedRatio(): void
    {
        $gauge = new Gauge(75.0, 50.0, 100.0, '%', '#123456', 'Confidence', 'Current level.', 480.0, 260.0);

        self::assertSame(0.5, $gauge->ratio());
        self::assertSame(480.0, $gauge->width());
        self::assertSame(260.0, $gauge->height());
        self::assertSame('Confidence', $gauge->title());
        self::assertSame('Current level.', $gauge->description());
    }

    #[Test]
    public function rejectsNonFiniteValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Gauge(INF, 0.0, 100.0);
    }

    #[Test]
    public function rejectsAnInvertedRange(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Gauge(50.0, 100.0, 0.0);
    }

    #[Test]
    public function rejectsAValueOutsideItsRange(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Gauge(110.0, 0.0, 100.0);
    }

    #[Test]
    public function rejectsEmptyOptionalTextAndColor(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Gauge(50.0, 0.0, 100.0, color: ' ');
    }

    #[Test]
    public function rejectsAnEmptyTitle(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Gauge(50.0, 0.0, 100.0, chartTitle: ' ');
    }

    #[Test]
    public function rejectsAnEmptyDescription(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Gauge(50.0, 0.0, 100.0, chartDescription: ' ');
    }

    #[Test]
    public function rejectsAnInvalidSize(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Gauge(50.0, 0.0, 100.0, chartWidth: 200.0);
    }

    #[Test]
    public function exposesComparableSeriesAndTheirRatios(): void
    {
        $gauge = new Gauge(
            78.0,
            0.0,
            100.0,
            '%',
            label: 'Overall',
            comparisons: [new GaugeSeries('Quality', 86.0), new GaugeSeries('Readiness', 71.0)],
        );

        self::assertSame(['Overall', 'Quality', 'Readiness'], array_column($gauge->series(), 'name'));
        self::assertSame(0.86, $gauge->ratioFor($gauge->series()[1]));
    }

    #[Test]
    public function rejectsMoreThanThreeSeries(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Gauge(
            78.0,
            0.0,
            100.0,
            comparisons: [
                new GaugeSeries('A', 10.0),
                new GaugeSeries('B', 20.0),
                new GaugeSeries('C', 30.0),
            ],
        );
    }

    #[Test]
    public function rejectsAnOverflowingRangeInsteadOfReportingAnIncorrectRatio(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Gauge(0.0, -1.0e308, 1.0e308);
    }
}
