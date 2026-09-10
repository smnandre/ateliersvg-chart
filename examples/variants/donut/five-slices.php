<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::donut()
    ->title('Five parts of a total')
    ->description('Five spending categories surround the total, using the warm theme palette.')
    ->slice('People', 46)
    ->slice('Hosting', 22)
    ->slice('Sales', 16)
    ->slice('Support', 10)
    ->slice('Other', 6)
    ->build();

echo Chart::render($model, Atelier\Chart\Theme\Theme::warm());
