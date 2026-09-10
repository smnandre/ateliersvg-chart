<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::sparkline([-8, -3, 4, -2, 7, 3, 12, 6])
    ->title('A compact signed sequence')
    ->description('size(160, 48) fits gains and losses into a small inline chart; no axes are drawn.')
    ->size(160, 48)
    ->color('#06bfa8')
    ->build();

echo Chart::render($model);
