<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require_once dirname(__DIR__, 3).'/vendor/autoload.php';

$model = Chart::strip()
    ->title('Editing sequence')
    ->description('Draft, review, another draft, then publication.')
    ->segment(8, 'Draft')
    ->segment(3, 'Review')
    ->segment(5, 'Draft')
    ->segment(2, 'Publish')
    ->build();

echo Chart::render($model, Atelier\Chart\Theme\Theme::warm()->with(
    palette: ['#2f7d77', '#d46345', '#755f9f'],
));
