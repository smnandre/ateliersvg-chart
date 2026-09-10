<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::bubble()
    ->title('Three market groups')
    ->description('Three explicitly colored series separate market groups while bubble area still encodes revenue.')
    ->axes('Growth %', 'Margin %')
    ->series('Core', [['x' => 12, 'y' => 62, 'size' => 420], ['x' => 28, 'y' => 48, 'size' => 270]], '#06bfa8')
    ->series('New', [['x' => 43, 'y' => 30, 'size' => 100], ['x' => 57, 'y' => 43, 'size' => 190]], '#f4a34b')
    ->series('Partner', [['x' => 35, 'y' => 74, 'size' => 240], ['x' => 68, 'y' => 64, 'size' => 130]], '#a58fff')
    ->build();

echo Chart::render($model);
