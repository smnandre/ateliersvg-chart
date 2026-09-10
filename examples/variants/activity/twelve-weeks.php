<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$values = [];
$start = new DateTimeImmutable('2026-06-01');

for ($day = 0; $day < 84; ++$day) {
    $values[$start->modify('+'.$day.' days')->format('M j')] = ($day * 7 + intdiv($day, 7)) % 19;
}

$model = Chart::activity()
    ->title('Twelve weeks on a wider canvas')
    ->description('Daily activity from Monday June 1 across twelve weeks.')
    ->size(900, 280)
    ->series('Changes', $values)
    ->build();

echo Chart::render($model);
