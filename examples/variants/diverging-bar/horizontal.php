<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::divergingBar()
    ->title('Horizontal comparison')
    ->description('horizontal() sends the first series right and the second left; all input magnitudes remain positive.')
    ->horizontal()
    ->series('Approve', ['North' => 64, 'South' => 48, 'East' => 72, 'West' => 56])
    ->series('Disapprove', ['North' => 36, 'South' => 52, 'East' => 28, 'West' => 44])
    ->build();

echo Chart::render($model);
