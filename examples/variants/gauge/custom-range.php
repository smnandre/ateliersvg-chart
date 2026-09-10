<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::gauge(175)
    ->title('A range in milliseconds')
    ->description('range(0, 250) makes 175 ms occupy seventy percent of the arc.')
    ->range(0, 250)
    ->unit(' ms')
    ->color('#06bfa8')
    ->label('Latency')
    ->size(480, 280)
    ->build();

echo Chart::render($model);
