<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::activity()
    ->title('Colors for selected cells')
    ->description('Two cells override the series color at full opacity, and summary() labels the strip.')
    ->strip()
    ->size(720, 220)
    ->summary('Release on Thursday; rollback on Friday')
    ->series('Deploys', [
        'Mon' => 2,
        'Tue' => 4,
        'Wed' => 3,
        'Thu' => ['value' => 8, 'color' => '#06bfa8', 'label' => 'Release'],
        'Fri' => ['value' => 1, 'color' => '#f4a34b', 'label' => 'Rollback'],
        'Sat' => 0,
        'Sun' => 2,
    ])
    ->build();

echo Chart::render($model);
