<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::radar()
    ->title('One profile on a five-point scale')
    ->description('domain(0, 5) fixes the outer ring at five for all five qualities.')
    ->domain(0, 5)
    ->series('Studio', ['Speed' => 4, 'Ease' => 5, 'Reach' => 3, 'Trust' => 4, 'Cost' => 2], '#06bfa8')
    ->build();

echo Chart::render($model);
