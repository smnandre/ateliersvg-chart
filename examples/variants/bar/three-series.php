<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::bar()
    ->title('Three series without value labels')
    ->description('Three bars per quarter share a legend; showValues(false) removes the numeric labels.')
    ->showValues(false)
    ->series('Web', ['Q1' => 24, 'Q2' => 38, 'Q3' => 31, 'Q4' => 52])
    ->series('Retail', ['Q1' => 18, 'Q2' => 22, 'Q3' => 29, 'Q4' => 35])
    ->series('Partners', ['Q1' => 9, 'Q2' => 16, 'Q3' => 12, 'Q4' => 24])
    ->build();

echo Chart::render($model);
