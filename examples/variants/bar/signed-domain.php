<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::bar()
    ->title('Positive and negative values')
    ->description('A fixed -30 to 50 domain puts gains above zero and losses below it.')
    ->domain(-30, 50)
    ->series('Net change', ['Jan' => 32, 'Feb' => -18, 'Mar' => 44, 'Apr' => -12, 'May' => 26])
    ->build();

echo Chart::render($model);
