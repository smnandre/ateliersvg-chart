<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$values = [];
$start = new DateTimeImmutable('2026-06-01');

for ($day = 0; $day < 28; ++$day) {
    $values[$start->modify('+'.$day.' days')->format('j')] = ($day * 5) % 11;
}

$model = Chart::activity()
    ->title('Four weeks in a calendar')
    ->description('Daily commits from Monday June 1 across four weeks.')
    ->calendar()
    ->series('Commits', $values)
    ->build();

echo Chart::render($model);
