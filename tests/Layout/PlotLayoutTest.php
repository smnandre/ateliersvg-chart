<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Layout;

use Atelier\Chart\Layout\PlotLayout;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PlotLayout::class)]
final class PlotLayoutTest extends TestCase
{
    #[Test]
    public function reservesMoreTopSpaceForALegend(): void
    {
        $layout = new PlotLayout();
        $withoutLegend = $layout->cartesian(720.0, 420.0, false);
        $withLegend = $layout->cartesian(720.0, 420.0, true);

        self::assertSame(64.0, $withoutLegend->plot->y);
        self::assertSame(86.0, $withLegend->plot->y);
        self::assertSame(62.0, $withLegend->plot->x);
        self::assertSame(632.0, $withLegend->plot->width);
    }

    #[Test]
    public function reservesSpaceForHorizontalCategoryLabels(): void
    {
        $frame = (new PlotLayout())->horizontalBar(720.0, 420.0, true);

        self::assertSame(108.0, $frame->plot->x);
        self::assertSame(582.0, $frame->plot->width);
        self::assertSame(362.0, $frame->plot->bottom());
    }
}
