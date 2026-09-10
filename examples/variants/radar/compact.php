<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::radar()
    ->title('A compact square canvas')
    ->description('size(400, 400) puts two explicitly colored profiles on a square canvas.')
    ->size(400, 400)
    ->domain(0, 100)
    ->series('Now', ['Speed' => 65, 'Ease' => 85, 'Reach' => 50, 'Trust' => 75], '#06bfa8')
    ->series('Goal', ['Speed' => 90, 'Ease' => 90, 'Reach' => 85, 'Trust' => 95], '#f4a34b')
    ->build();

echo Chart::render($model);
