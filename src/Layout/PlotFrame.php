<?php

declare(strict_types=1);

namespace Atelier\Chart\Layout;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Layout\Geometry\Rect;

final readonly class PlotFrame
{
    public function __construct(
        public Rect $canvas,
        public Rect $plot,
        public float $titleBaseline,
        public float $legendBaseline,
        public float $categoryBaseline,
    ) {
        if (!$canvas->containsRect($plot)) {
            throw new InvalidArgumentException('The plot rectangle must fit inside its canvas.');
        }
    }
}
