<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\Point;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Point::class)]
final class PointTest extends TestCase
{
    #[Test]
    public function exposesCoordinatesAndSize(): void
    {
        $point = new Point(12.0, 24.0, 80.0);

        self::assertSame(12.0, $point->x);
        self::assertSame(24.0, $point->y);
        self::assertSame(80.0, $point->size);
    }

    #[Test]
    public function rejectsANonPositiveSize(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Point(12.0, 24.0, 0.0);
    }

    #[Test]
    public function rejectsNonFiniteCoordinates(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Point(INF, 24.0);
    }
}
