<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::gauge(68)
    ->title('Current and target')
    ->description('Adding one series draws a second arc for the target on the same 0 to 100 range.')
    ->label('Current')
    ->series('Target', 90)
    ->unit('%')
    ->size(480, 280)
    ->build();

echo Chart::render($model);
