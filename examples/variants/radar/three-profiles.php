<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::radar()
    ->title('Three profiles on one scale')
    ->description('Three polygons use the same 0 to 100 scale so their shapes can be compared directly.')
    ->domain(0, 100)
    ->series('Basic', ['Speed' => 70, 'Ease' => 90, 'Reach' => 40, 'Trust' => 65, 'Cost' => 95])
    ->series('Pro', ['Speed' => 85, 'Ease' => 75, 'Reach' => 75, 'Trust' => 85, 'Cost' => 65])
    ->series('Team', ['Speed' => 80, 'Ease' => 60, 'Reach' => 95, 'Trust' => 95, 'Cost' => 40])
    ->build();

echo Chart::render($model);
