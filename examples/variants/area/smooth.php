<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::area()
    ->title('A smooth filled curve')
    ->description('smooth() rounds the upper edge while the fill returns to the zero baseline.')
    ->smooth()
    ->series('Downloads', ['Mon' => 12, 'Tue' => 28, 'Wed' => 19, 'Thu' => 36, 'Fri' => 26, 'Sat' => 44])
    ->build();

echo Chart::render($model);
