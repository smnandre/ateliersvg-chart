<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::scatter()
    ->title('Three cohorts')
    ->description('Three colored point series compare the same two measurements across cohorts.')
    ->axes('Study hours', 'Score')
    ->series('Morning', [['x' => 2, 'y' => 48], ['x' => 5, 'y' => 66], ['x' => 8, 'y' => 83]])
    ->series('Evening', [['x' => 3, 'y' => 40], ['x' => 6, 'y' => 59], ['x' => 9, 'y' => 76]])
    ->series('Weekend', [['x' => 2, 'y' => 61], ['x' => 4, 'y' => 78], ['x' => 7, 'y' => 92]])
    ->build();

echo Chart::render($model);
