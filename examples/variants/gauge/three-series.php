<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::gauge(86)
    ->title('Three labeled readings')
    ->description('The main value and two comparison series form three separately colored arcs.')
    ->label('API')
    ->color('#06bfa8')
    ->series('Web', 72, '#f4a34b')
    ->series('Jobs', 58, '#a58fff')
    ->unit('%')
    ->size(540, 320)
    ->build();

echo Chart::render($model);
