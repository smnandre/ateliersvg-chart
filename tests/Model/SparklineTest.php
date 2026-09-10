<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\Sparkline;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Sparkline::class)]
final class SparklineTest extends TestCase
{
    #[Test]
    public function exposesCompactChartMetadata(): void
    {
        $chart = new Sparkline([1.0, 3.0], 'Trend', 'Rising.', 180.0, 48.0, '#5a67d8');

        self::assertSame(180.0, $chart->width());
        self::assertSame(48.0, $chart->height());
        self::assertSame('Trend', $chart->title());
        self::assertSame('Rising.', $chart->description());
        self::assertSame('#5a67d8', $chart->color);
    }

    #[Test]
    public function requiresTwoValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Sparkline([1.0]);
    }

    #[Test]
    public function rejectsNonFiniteValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Sparkline([1.0, NAN]);
    }

    #[Test]
    public function rejectsSmallDimensions(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Sparkline([1.0, 2.0], chartWidth: 60.0);
    }
}
