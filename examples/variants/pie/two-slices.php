<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::pie()
    ->title('Two explicitly colored slices')
    ->description('Two slices split the circle 72 to 28, with colors supplied to slice().')
    ->slice('Renewed', 72, '#06bfa8')
    ->slice('Churned', 28, '#f4a34b')
    ->build();

echo Chart::render($model);
