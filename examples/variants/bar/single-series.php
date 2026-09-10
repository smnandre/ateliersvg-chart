<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::bar()
    ->title('One series, explicit color')
    ->description('One teal bar per quarter, with each value printed above its bar.')
    ->series('Revenue', ['Q1' => 24, 'Q2' => 38, 'Q3' => 31, 'Q4' => 52], '#06bfa8')
    ->build();

echo Chart::render($model);
