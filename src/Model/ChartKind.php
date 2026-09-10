<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

enum ChartKind: string
{
    case Area = 'area';
    case Bar = 'bar';
    case Line = 'line';
    case Radar = 'radar';

    public function defaultTitle(): string
    {
        return match ($this) {
            self::Area => 'Area chart',
            self::Bar => 'Bar chart',
            self::Line => 'Line chart',
            self::Radar => 'Radar chart',
        };
    }
}
