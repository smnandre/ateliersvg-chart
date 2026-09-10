<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__).'/vendor/autoload.php';

$model = Chart::strip()
    ->title('Production cycle')
    ->description('Production, pause, then production again, weighted 12 to 3 to 9.')
    ->segment(weight: 12, label: 'Production', color: '#06bfa8')
    ->segment(weight: 3, label: 'Pause', color: '#f4a34b')
    ->segment(weight: 9, label: 'Production', color: '#06bfa8')
    ->build();

echo Chart::render($model);
