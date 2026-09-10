<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Layout;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Layout\PlotFrame;
use Atelier\Layout\Geometry\Rect;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PlotFrame::class)]
final class PlotFrameTest extends TestCase
{
    #[Test]
    public function preservesCanvasAndPlotGeometry(): void
    {
        $canvas = Rect::fromSize(200.0, 100.0);
        $plot = new Rect(20.0, 10.0, 160.0, 70.0);
        $frame = new PlotFrame($canvas, $plot, 20.0, 30.0, 95.0);

        self::assertSame($plot, $frame->plot);
        self::assertSame(95.0, $frame->categoryBaseline);
    }

    #[Test]
    public function rejectsAPlotOutsideItsCanvas(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PlotFrame(Rect::fromSize(100.0, 100.0), new Rect(80.0, 80.0, 40.0, 40.0), 10.0, 20.0, 90.0);
    }
}
