<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::stackedBar()
    ->title('Horizontal stacks')
    ->description('horizontal() places category labels on the left and stacks three components across each row.')
    ->horizontal()
    ->showValues()
    ->series('Shipped', ['Platform' => 48, 'Studio' => 24, 'Docs' => 39, 'Mobile' => 44])
    ->series('Building', ['Platform' => 18, 'Studio' => 38, 'Docs' => 16, 'Mobile' => 22])
    ->series('Planned', ['Platform' => 12, 'Studio' => 26, 'Docs' => 31, 'Mobile' => 17])
    ->build();

echo Chart::render($model);
