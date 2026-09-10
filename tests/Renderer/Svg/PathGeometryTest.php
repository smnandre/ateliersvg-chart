<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Renderer\Svg;

use Atelier\Chart\Renderer\Svg\PathGeometry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PathGeometry::class)]
final class PathGeometryTest extends TestCase
{
    #[Test]
    public function createsStraightSmoothRadialAndArcPaths(): void
    {
        $geometry = new PathGeometry();
        $points = [[0.0, 10.0, 1.0], [10.0, 20.0, 2.0], [20.0, 15.0, 3.0]];

        self::assertStringContainsString(' L ', $geometry->cartesianLine($points, false));
        self::assertStringContainsString(' C ', $geometry->cartesianLine($points, true));
        self::assertStringEndsWith(' Z', $geometry->radarPolygon(10.0, 10.0, 5.0, 3));
        self::assertStringContainsString(' A ', $geometry->pieSlice(10.0, 10.0, 8.0, 4.0, 0.0, M_PI));
        self::assertStringContainsString(' A ', $geometry->gaugeArc(10.0, 10.0, 8.0, 0.5));
        self::assertSame(13.5, $geometry->bubbleRadius(25.0, 100.0));
    }

    #[Test]
    public function createsCompletePieAndDonutCircles(): void
    {
        $geometry = new PathGeometry();

        self::assertSame(2, substr_count($geometry->pieSlice(10.0, 10.0, 8.0, 0.0, 0.0, 2.0 * M_PI), ' A '));
        self::assertSame(4, substr_count($geometry->pieSlice(10.0, 10.0, 8.0, 4.0, 0.0, 2.0 * M_PI), ' A '));
    }
}
