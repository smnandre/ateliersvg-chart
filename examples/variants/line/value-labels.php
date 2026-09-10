<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::line()
    ->title('Values at each point')
    ->description('showValues() prints the five measurements along a single straight-segment line.')
    ->showValues()
    ->series('Latency', ['Mon' => 42, 'Tue' => 35, 'Wed' => 48, 'Thu' => 31, 'Fri' => 27])
    ->build();

echo Chart::render($model);
