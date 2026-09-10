<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::scatter()
    ->title('One point series')
    ->description('A single series plots study hours against scores, with explicit axis labels.')
    ->axes('Study hours', 'Score')
    ->series('Students', [['x' => 2, 'y' => 48], ['x' => 4, 'y' => 61], ['x' => 5, 'y' => 58], ['x' => 7, 'y' => 79], ['x' => 9, 'y' => 91]])
    ->build();

echo Chart::render($model);
