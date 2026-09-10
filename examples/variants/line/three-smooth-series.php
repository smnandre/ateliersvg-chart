<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::line()
    ->title('Three smooth series')
    ->description('smooth() connects three weekly series with curves; the legend names each channel.')
    ->smooth()
    ->series('Search', ['Mon' => 18, 'Tue' => 30, 'Wed' => 24, 'Thu' => 42, 'Fri' => 36])
    ->series('Direct', ['Mon' => 12, 'Tue' => 18, 'Wed' => 29, 'Thu' => 23, 'Fri' => 34])
    ->series('Referral', ['Mon' => 8, 'Tue' => 14, 'Wed' => 11, 'Thu' => 20, 'Fri' => 17])
    ->build();

echo Chart::render($model);
