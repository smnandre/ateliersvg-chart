<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

enum ActivityLayout: string
{
    case Calendar = 'calendar';
    case Strip = 'strip';
}
