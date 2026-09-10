<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::bubble()
    ->title('Size as a third measurement')
    ->description('Four markets share one color; their size values control bubble area.')
    ->axes('Growth %', 'Margin %')
    ->series('Markets', [['x' => 12, 'y' => 25, 'size' => 40], ['x' => 28, 'y' => 55, 'size' => 160], ['x' => 45, 'y' => 35, 'size' => 360], ['x' => 62, 'y' => 68, 'size' => 640]])
    ->build();

echo Chart::render($model);
