<?php

declare(strict_types=1);

namespace Atelier\Chart\Layout;

use Atelier\Layout\Geometry\Insets;
use Atelier\Layout\Geometry\Rect;

final readonly class PlotLayout
{
    public function cartesian(float $width, float $height, bool|int $legendRows = 0): PlotFrame
    {
        $canvas = Rect::fromSize($width, $height);
        $rows = is_bool($legendRows) ? ($legendRows ? 1 : 0) : $legendRows;
        $top = 64.0 + 22.0 * $rows;
        $plot = $canvas->inset(new Insets($top, 26.0, 54.0, 62.0));

        if ($plot->width < 1.0 || $plot->height < 40.0) {
            throw new \Atelier\Chart\Exception\InvalidArgumentException('The chart is too small for its legend and plot. Increase its dimensions or reduce the number of series.');
        }

        return new PlotFrame(
            $canvas,
            $plot,
            34.0,
            61.0,
            $plot->bottom() + 28.0,
        );
    }

    public function horizontalBar(float $width, float $height, bool|int $legendRows = 0): PlotFrame
    {
        $canvas = Rect::fromSize($width, $height);
        $rows = is_bool($legendRows) ? ($legendRows ? 1 : 0) : $legendRows;
        $top = 64.0 + 22.0 * $rows;
        $plot = $canvas->inset(new Insets($top, 30.0, 58.0, 108.0));

        if ($plot->width < 1.0 || $plot->height < 40.0) {
            throw new \Atelier\Chart\Exception\InvalidArgumentException('The chart is too small for its legend and plot. Increase its dimensions or reduce the number of series.');
        }

        return new PlotFrame(
            $canvas,
            $plot,
            34.0,
            61.0,
            $plot->bottom() + 32.0,
        );
    }
}
