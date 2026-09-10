<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::line()
    ->title('A focused vertical scale')
    ->description('domain(95, 105) shows small changes around 100 instead of extending the axis to zero.')
    ->domain(95, 105)
    ->showValues()
    ->series('Index', ['Mon' => 98, 'Tue' => 101, 'Wed' => 99, 'Thu' => 104, 'Fri' => 102], '#06bfa8')
    ->build();

echo Chart::render($model);
