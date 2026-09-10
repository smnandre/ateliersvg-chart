<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::stackedBar()
    ->title('Three components without labels')
    ->description('showValues(false) leaves the colored segments and legend without numbers inside the bars.')
    ->showValues(false)
    ->series('Product', ['Jan' => 38, 'Feb' => 52, 'Mar' => 45, 'Apr' => 63])
    ->series('Services', ['Jan' => 24, 'Feb' => 18, 'Mar' => 32, 'Apr' => 22])
    ->series('Partners', ['Jan' => 11, 'Feb' => 17, 'Mar' => 9, 'Apr' => 23])
    ->build();

echo Chart::render($model);
