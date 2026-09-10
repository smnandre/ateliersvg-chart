<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::divergingBar()
    ->title('Values on both sides')
    ->description('showValues() labels the magnitudes above and below the zero line.')
    ->showValues()
    ->series('Incoming', ['Mon' => 24, 'Tue' => 38, 'Wed' => 18, 'Thu' => 32])
    ->series('Outgoing', ['Mon' => 18, 'Tue' => 22, 'Wed' => 30, 'Thu' => 26])
    ->build();

echo Chart::render($model);
