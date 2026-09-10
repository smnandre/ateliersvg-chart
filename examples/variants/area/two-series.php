<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::area()
    ->title('Two overlapping series')
    ->description('Two translucent areas share the same baseline; their values are not stacked.')
    ->series('Desktop', ['Mon' => 36, 'Tue' => 52, 'Wed' => 42, 'Thu' => 63, 'Fri' => 48])
    ->series('Mobile', ['Mon' => 18, 'Tue' => 30, 'Wed' => 26, 'Thu' => 39, 'Fri' => 34])
    ->build();

echo Chart::render($model);
