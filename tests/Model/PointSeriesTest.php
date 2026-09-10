<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\Point;
use Atelier\Chart\Model\PointSeries;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PointSeries::class)]
final class PointSeriesTest extends TestCase
{
    #[Test]
    public function exposesPointsAndColor(): void
    {
        $series = new PointSeries('Enterprise', [new Point(12.0, 24.0)], '#123456');

        self::assertSame('Enterprise', $series->name);
        self::assertCount(1, $series->points);
        self::assertSame('#123456', $series->color);
    }

    #[Test]
    public function rejectsAnEmptySeries(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PointSeries('Enterprise', []);
    }

    #[Test]
    public function rejectsAnEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PointSeries(' ', [new Point(12.0, 24.0)]);
    }

    #[Test]
    public function rejectsAnEmptyColor(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PointSeries('Enterprise', [new Point(12.0, 24.0)], ' ');
    }
}
