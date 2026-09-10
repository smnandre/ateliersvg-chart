<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = (new Atelier\Chart\Builder\PieChartBuilder(0.35))
    ->title('A thicker ring')
    ->description('PieChartBuilder(0.35) leaves a smaller hole than the default donut ratio of 0.58.')
    ->slice('Direct', 42)
    ->slice('Search', 31)
    ->slice('Referral', 18)
    ->slice('Social', 9)
    ->build();

echo Chart::render($model);
