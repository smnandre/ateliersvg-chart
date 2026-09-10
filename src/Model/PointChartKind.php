<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

enum PointChartKind: string
{
    case Bubble = 'bubble';
    case Scatter = 'scatter';

    public function defaultTitle(): string
    {
        return match ($this) {
            self::Bubble => 'Bubble chart',
            self::Scatter => 'Scatter plot',
        };
    }
}
