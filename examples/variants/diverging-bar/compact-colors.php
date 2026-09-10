<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::divergingBar()
    ->title('Compact with explicit colors')
    ->description('A 420 by 320 canvas uses teal and orange for two opposing series.')
    ->size(420, 320)
    ->series('Incoming', ['Mon' => 24, 'Tue' => 38, 'Wed' => 18], '#06bfa8')
    ->series('Outgoing', ['Mon' => 18, 'Tue' => 22, 'Wed' => 30], '#f4a34b')
    ->build();

echo Chart::render($model);
