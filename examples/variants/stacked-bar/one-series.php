<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::stackedBar()
    ->title('A stack with one series')
    ->description('One series produces a single segment per category, ready for more components to be added.')
    ->showValues()
    ->series('Product', ['Jan' => 38, 'Feb' => 52, 'Mar' => 45, 'Apr' => 63])
    ->build();

echo Chart::render($model);
