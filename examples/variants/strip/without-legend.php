<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::strip()
    ->title('Material composition')
    ->description('Four materials weighted 4 to 2 to 3 to 1.')
    ->withoutLegend()
    ->size(480, 160)
    ->segment(4, 'Paper')
    ->segment(2, 'Ink')
    ->segment(3, 'Fabric')
    ->segment(1, 'Glue')
    ->build();

echo Chart::render($model);
