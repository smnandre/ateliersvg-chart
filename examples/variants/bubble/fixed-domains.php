<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::bubble()
    ->title('A shared comparison window')
    ->description('Fixed 0 to 100 axes leave the same comparison window available for other datasets.')
    ->axes('Growth %', 'Margin %')
    ->xDomain(0, 100)
    ->yDomain(0, 100)
    ->series('Markets', [['x' => 22, 'y' => 34, 'size' => 80], ['x' => 46, 'y' => 58, 'size' => 220], ['x' => 72, 'y' => 76, 'size' => 420]])
    ->build();

echo Chart::render($model);
