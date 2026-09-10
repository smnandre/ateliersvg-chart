<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::pie()
    ->title('A compact pie')
    ->description('size(400, 320) fits three slices and their legend into a smaller canvas.')
    ->size(400, 320)
    ->slice('Web', 55)
    ->slice('Store', 30)
    ->slice('Partners', 15)
    ->build();

echo Chart::render($model);
