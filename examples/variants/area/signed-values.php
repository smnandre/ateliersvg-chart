<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::area()
    ->title('An area crossing zero')
    ->description('A fixed -20 to 30 domain and value labels show the series crossing the zero baseline.')
    ->domain(-20, 30)
    ->showValues()
    ->series('Balance', ['Mon' => 18, 'Tue' => -12, 'Wed' => 24, 'Thu' => -8, 'Fri' => 16])
    ->build();

echo Chart::render($model);
