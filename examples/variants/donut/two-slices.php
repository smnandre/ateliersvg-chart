<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::donut()
    ->title('Progress as two slices')
    ->description('Completed and remaining work occupy 84 and 36 units; the center shows their total of 120.')
    ->slice('Done', 84, '#06bfa8')
    ->slice('Remaining', 36, '#6b7280')
    ->build();

echo Chart::render($model);
