<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::strip()
    ->title('Four stages')
    ->description('Four stages with equal weights.')
    ->segment(1, 'Research')
    ->segment(1, 'Design')
    ->segment(1, 'Build')
    ->segment(1, 'Review')
    ->build();

echo Chart::render($model);
