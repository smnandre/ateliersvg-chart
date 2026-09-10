<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::sparkline([42, 38, 40, 31, 28, 30, 22, 18])
    ->title('A falling trend in orange')
    ->description('color() gives this falling sequence an explicit orange stroke.')
    ->color('#f4a34b')
    ->build();

echo Chart::render($model);
