<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::sparkline([18, 24, 21, 32, 28, 36, 30, 42, 38, 46, 40, 54, 49, 58, 52, 64])
    ->title('A wider trend')
    ->description('size(480, 100) gives sixteen observations more horizontal space.')
    ->size(480, 100)
    ->build();

echo Chart::render($model);
