<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::scatter()
    ->title('Fixed bounds on both axes')
    ->description('xDomain(-10, 10) and yDomain(-10, 10) show signed offsets on matching scales.')
    ->axes('Horizontal offset', 'Vertical offset')
    ->xDomain(-10, 10)
    ->yDomain(-10, 10)
    ->series('Samples', [['x' => -7, 'y' => 5], ['x' => -4, 'y' => -6], ['x' => 1, 'y' => 3], ['x' => 5, 'y' => -2], ['x' => 8, 'y' => 7]], '#06bfa8')
    ->build();

echo Chart::render($model);
