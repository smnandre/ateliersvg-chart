<?php

declare(strict_types=1);

use Atelier\Chart\Chart;
use Atelier\Chart\Theme\Theme;

require dirname(__DIR__).'/vendor/autoload.php';

$output = __DIR__.'/output';

$bar = Chart::bar()
    ->title('Quarterly revenue')
    ->description('Revenue by quarter for 2025 and 2026.')
    ->series('2025', ['Q1' => 18, 'Q2' => 32, 'Q3' => 24, 'Q4' => 42])
    ->series('2026', ['Q1' => 26, 'Q2' => 24, 'Q3' => 38, 'Q4' => 35])
    ->build();

$line = Chart::line()
    ->title('Weekly signups')
    ->description('Organic and referral signups from Monday to Sunday.')
    ->smooth()
    ->series('Organic', ['Mon' => 12, 'Tue' => 21, 'Wed' => 16, 'Thu' => 28, 'Fri' => 23, 'Sat' => 34, 'Sun' => 29])
    ->series('Referral', ['Mon' => 18, 'Tue' => 14, 'Wed' => 24, 'Thu' => 19, 'Fri' => 31, 'Sat' => 27, 'Sun' => 38])
    ->build();

$area = Chart::area()
    ->title('Daily traffic')
    ->description('Visits over eight days.')
    ->series('Visits', ['01' => 12, '02' => 17, '03' => 14, '04' => 24, '05' => 29, '06' => 26, '07' => 35, '08' => 41])
    ->build();

$sparkline = Chart::sparkline([14, 18, 16, 24, 22, 31, 36, 33, 42])
    ->title('Nine day trend')
    ->description('A generally rising nine day series.')
    ->build();

$radar = Chart::radar()
    ->title('Product profile')
    ->description('Current product qualities compared with the target profile.')
    ->series('Current', ['Clarity' => 82, 'Speed' => 92, 'Reach' => 61, 'Trust' => 88, 'Craft' => 70])
    ->series('Target', ['Clarity' => 94, 'Speed' => 74, 'Reach' => 86, 'Trust' => 76, 'Craft' => 92])
    ->build();

$gauge = Chart::gauge(78)
    ->title('Release confidence')
    ->description('Current release confidence on a zero to one hundred scale.')
    ->label('Overall')
    ->series('Quality', 86)
    ->series('Readiness', 71)
    ->unit('%')
    ->size(480.0, 280.0)
    ->build();

$stackedVertical = Chart::stackedBar()
    ->title('Revenue mix')
    ->description('Monthly revenue split across product, services, and partners.')
    ->series('Product', ['Jan' => 38, 'Feb' => 52, 'Mar' => 45, 'Apr' => 63, 'May' => 58, 'Jun' => 74, 'Jul' => 69])
    ->series('Services', ['Jan' => 24, 'Feb' => 18, 'Mar' => 32, 'Apr' => 22, 'May' => 41, 'Jun' => 29, 'Jul' => 47])
    ->series('Partners', ['Jan' => 11, 'Feb' => 17, 'Mar' => 9, 'Apr' => 23, 'May' => 14, 'Jun' => 26, 'Jul' => 18])
    ->build();

$stackedHorizontal = Chart::stackedBar()
    ->horizontal()
    ->title('Roadmap allocation')
    ->description('Work allocation by product area and delivery state.')
    ->series('Shipped', ['Platform' => 48, 'Studio' => 24, 'Docs' => 39, 'Labs' => 18, 'Mobile' => 44, 'Cloud' => 27])
    ->series('In progress', ['Platform' => 18, 'Studio' => 38, 'Docs' => 16, 'Labs' => 35, 'Mobile' => 22, 'Cloud' => 41])
    ->series('Planned', ['Platform' => 12, 'Studio' => 26, 'Docs' => 31, 'Labs' => 21, 'Mobile' => 17, 'Cloud' => 24])
    ->build();

$pie = Chart::pie()
    ->title('Revenue channels')
    ->description('Revenue share across four sales channels.')
    ->slice('Subscriptions', 48)
    ->slice('Services', 27)
    ->slice('Partners', 17)
    ->slice('Licensing', 8)
    ->build();

$donut = Chart::donut()
    ->title('Traffic sources')
    ->description('Traffic share across acquisition sources.')
    ->slice('Direct', 42)
    ->slice('Search', 31)
    ->slice('Referral', 18)
    ->slice('Social', 9)
    ->build();

$scatter = Chart::scatter()
    ->title('Customer distribution')
    ->description('Customer age compared with annual spend.')
    ->axes('Age', 'Annual spend')
    ->series('New', [
        ['x' => 22, 'y' => 28],
        ['x' => 27, 'y' => 34],
        ['x' => 31, 'y' => 41],
        ['x' => 38, 'y' => 47],
        ['x' => 45, 'y' => 58],
    ])
    ->series('Returning', [
        ['x' => 25, 'y' => 82],
        ['x' => 34, 'y' => 70],
        ['x' => 43, 'y' => 55],
        ['x' => 52, 'y' => 43],
        ['x' => 60, 'y' => 34],
    ])
    ->build();

$bubble = Chart::bubble()
    ->title('Market opportunities')
    ->description('Growth and margin by market, with revenue encoded as bubble area.')
    ->axes('Growth %', 'Margin %')
    ->series('Core', [
        ['x' => 18, 'y' => 62, 'size' => 420],
        ['x' => 28, 'y' => 54, 'size' => 270],
        ['x' => 36, 'y' => 71, 'size' => 190],
    ])
    ->series('Emerging', [
        ['x' => 48, 'y' => 38, 'size' => 120],
        ['x' => 56, 'y' => 49, 'size' => 210],
        ['x' => 67, 'y' => 32, 'size' => 85],
    ])
    ->build();

$diverging = Chart::divergingBar()
    ->title('Year over year')
    ->description('Monthly 2024 values above zero compared with 2023 magnitudes below zero.')
    ->series('2024', ['Jan' => 21, 'Feb' => 10, 'Mar' => 13, 'Apr' => 12, 'May' => 20, 'Jun' => 12, 'Jul' => 15])
    ->series('2023', ['Jan' => 13, 'Feb' => 16, 'Mar' => 14, 'Apr' => 10, 'May' => 17, 'Jun' => 13, 'Jul' => 12])
    ->build();

$pyramid = Chart::divergingBar()
    ->horizontal()
    ->title('Population profile')
    ->description('Women to the right and men to the left by age group.')
    ->series('Women', ['0-9' => 18, '10-19' => 22, '20-29' => 27, '30-39' => 31, '40-49' => 25, '50-59' => 20, '60+' => 16])
    ->series('Men', ['0-9' => 20, '10-19' => 21, '20-29' => 25, '30-39' => 28, '40-49' => 24, '50-59' => 18, '60+' => 13])
    ->build();

$activityValues = [];
$activityStart = new DateTimeImmutable('2026-06-29');

for ($day = 0; $day < 63; ++$day) {
    $date = $activityStart->modify('+'.$day.' days');
    $activityValues[$date->format('M j')] = 0 === $day % 11 ? 0 : (($day * 7 + intdiv($day, 4)) % 16) + 1;
}

$activity = Chart::activity()
    ->title('Release activity')
    ->description('Nine weeks of release and deployment activity.')
    ->series('Changes', $activityValues)
    ->build();

$requestValues = [];

for ($hour = 0; $hour < 24; ++$hour) {
    $requestValues[\sprintf('%02d:00', $hour)] = (int) round(
        260.0 + 640.0 * exp(-(($hour - 14) ** 2) / 26.0) + 120.0 * exp(-(($hour - 9) ** 2) / 8.0),
    );
}

$activityStrip = Chart::activity()
    ->title('Requests per hour')
    ->description('Request volume across one day, one cell per hour.')
    ->strip()
    ->summary('Peak at 14:00')
    ->size(720.0, 220.0)
    ->series('Requests', $requestValues)
    ->build();

// Reuse the runnable example as the canonical strip model.
ob_start();
require __DIR__.'/strip.php';
ob_end_clean();
$strip = $model;

$incidents = Chart::stackedBar()
    ->title('Incidents by severity')
    ->description('Weekly minor, major, and critical incidents.')
    ->series('Minor', ['W1' => 4, 'W2' => 7, 'W3' => 3, 'W4' => 6, 'W5' => 2, 'W6' => 8, 'W7' => 5, 'W8' => 3, 'W9' => 6, 'W10' => 2, 'W11' => 4, 'W12' => 3])
    ->series('Major', ['W1' => 2, 'W2' => 1, 'W3' => 4, 'W4' => 2, 'W5' => 3, 'W6' => 1, 'W7' => 2, 'W8' => 4, 'W9' => 1, 'W10' => 3, 'W11' => 2, 'W12' => 1])
    ->series('Critical', ['W1' => 0, 'W2' => 1, 'W3' => 0, 'W4' => 1, 'W5' => 0, 'W6' => 2, 'W7' => 0, 'W8' => 1, 'W9' => 0, 'W10' => 0, 'W11' => 1, 'W12' => 0])
    ->build();

$barMobile = Chart::bar()
    ->title('Quarterly revenue')
    ->description('Revenue by quarter for 2025 and 2026.')
    ->size(360.0, 280.0)
    ->series('2025', ['Q1' => 18, 'Q2' => 32, 'Q3' => 24, 'Q4' => 42])
    ->series('2026', ['Q1' => 26, 'Q2' => 24, 'Q3' => 38, 'Q4' => 35])
    ->build();

$lineMobile = Chart::line()
    ->title('Weekly signups')
    ->description('Organic and referral signups from Monday to Sunday.')
    ->size(360.0, 280.0)
    ->smooth()
    ->series('Organic', ['Mon' => 12, 'Tue' => 21, 'Wed' => 16, 'Thu' => 28, 'Fri' => 23, 'Sat' => 34, 'Sun' => 29])
    ->series('Referral', ['Mon' => 18, 'Tue' => 14, 'Wed' => 24, 'Thu' => 19, 'Fri' => 31, 'Sat' => 27, 'Sun' => 38])
    ->build();

$areaMobile = Chart::area()
    ->title('Daily traffic')
    ->description('Visits over eight days.')
    ->size(360.0, 280.0)
    ->series('Visits', ['01' => 12, '02' => 17, '03' => 14, '04' => 24, '05' => 29, '06' => 26, '07' => 35, '08' => 41])
    ->build();

$radarMobile = Chart::radar()
    ->title('Product profile')
    ->description('Current product qualities compared with the target profile.')
    ->size(360.0, 320.0)
    ->series('Current', ['Clarity' => 82, 'Speed' => 92, 'Reach' => 61, 'Trust' => 88, 'Craft' => 70])
    ->series('Target', ['Clarity' => 94, 'Speed' => 74, 'Reach' => 86, 'Trust' => 76, 'Craft' => 92])
    ->build();

$stackedVerticalMobile = Chart::stackedBar()
    ->title('Revenue mix')
    ->description('Monthly revenue split across product, services, and partners.')
    ->size(360.0, 300.0)
    ->series('Product', ['Jan' => 38, 'Feb' => 52, 'Mar' => 45, 'Apr' => 63, 'May' => 58, 'Jun' => 74, 'Jul' => 69])
    ->series('Services', ['Jan' => 24, 'Feb' => 18, 'Mar' => 32, 'Apr' => 22, 'May' => 41, 'Jun' => 29, 'Jul' => 47])
    ->series('Partners', ['Jan' => 11, 'Feb' => 17, 'Mar' => 9, 'Apr' => 23, 'May' => 14, 'Jun' => 26, 'Jul' => 18])
    ->build();

$stackedHorizontalMobile = Chart::stackedBar()
    ->horizontal()
    ->title('Roadmap allocation')
    ->description('Work allocation by product area and delivery state.')
    ->size(360.0, 320.0)
    ->series('Shipped', ['Platform' => 48, 'Studio' => 24, 'Docs' => 39, 'Labs' => 18, 'Mobile' => 44, 'Cloud' => 27])
    ->series('In progress', ['Platform' => 18, 'Studio' => 38, 'Docs' => 16, 'Labs' => 35, 'Mobile' => 22, 'Cloud' => 41])
    ->series('Planned', ['Platform' => 12, 'Studio' => 26, 'Docs' => 31, 'Labs' => 21, 'Mobile' => 17, 'Cloud' => 24])
    ->build();

$files = [
    'bar.svg' => Chart::render($bar),
    'theme-default.svg' => Chart::render($bar, Theme::default()),
    'theme-alto.svg' => Chart::render($bar, Theme::alto()),
    'theme-dark.svg' => Chart::render($bar, Theme::dark()),
    'theme-mono.svg' => Chart::render($bar, Theme::mono()),
    'theme-warm.svg' => Chart::render($bar, Theme::warm()),
    'line.svg' => Chart::render($line),
    'line-dark.svg' => Chart::render($line, Theme::dark()),
    'area.svg' => Chart::render($area),
    'area-warm.svg' => Chart::render($area, Theme::warm()),
    'sparkline.svg' => Chart::render($sparkline),
    'radar.svg' => Chart::render($radar),
    'gauge.svg' => Chart::render($gauge),
    'gauge-dark.svg' => Chart::render($gauge, Theme::dark()),
    'stacked-vertical.svg' => Chart::render($stackedVertical),
    'stacked-horizontal.svg' => Chart::render($stackedHorizontal, Theme::dark()),
    'pie.svg' => Chart::render($pie),
    'pie-alto.svg' => Chart::render($pie, Theme::alto()),
    'donut.svg' => Chart::render($donut),
    'donut-alto.svg' => Chart::render($donut, Theme::alto()),
    'scatter.svg' => Chart::render($scatter),
    'scatter-dark.svg' => Chart::render($scatter, Theme::dark()),
    'bubble.svg' => Chart::render($bubble),
    'bubble-warm.svg' => Chart::render($bubble, Theme::warm()),
    'diverging.svg' => Chart::render($diverging),
    'population-pyramid.svg' => Chart::render($pyramid),
    'activity.svg' => Chart::render($activity),
    'activity-strip.svg' => Chart::render($activityStrip),
    'strip.svg' => Chart::render($strip),
    'stacked-incidents.svg' => Chart::render($incidents),
    'bar-mobile.svg' => Chart::render($barMobile),
    'line-dark-mobile.svg' => Chart::render($lineMobile, Theme::dark()),
    'area-mobile.svg' => Chart::render($areaMobile),
    'area-warm-mobile.svg' => Chart::render($areaMobile, Theme::warm()),
    'radar-mobile.svg' => Chart::render($radarMobile),
    'stacked-vertical-mobile.svg' => Chart::render($stackedVerticalMobile),
    'stacked-horizontal-mobile.svg' => Chart::render($stackedHorizontalMobile, Theme::dark()),
];

foreach ($files as $name => $svg) {
    if (false === file_put_contents($output.'/'.$name, $svg)) {
        throw new RuntimeException('Unable to write '.$name.'.');
    }
}

$galleryThemes = [
    'default' => Theme::default(),
    'alto' => Theme::alto(),
    'dark' => Theme::dark(),
    'mono' => Theme::mono(),
    'warm' => Theme::warm(),
];

$galleryCharts = [
    ['slug' => 'bar', 'label' => 'Grouped bars', 'model' => $bar],
    ['slug' => 'line', 'label' => 'Line', 'model' => $line],
    ['slug' => 'area', 'label' => 'Area', 'model' => $area],
    ['slug' => 'radar', 'label' => 'Radar', 'model' => $radar],
    ['slug' => 'gauge', 'label' => 'Gauge', 'model' => $gauge],
    ['slug' => 'stacked-vertical', 'label' => 'Stacked vertical', 'model' => $stackedVertical],
    ['slug' => 'stacked-horizontal', 'label' => 'Stacked horizontal', 'model' => $stackedHorizontal],
    ['slug' => 'pie', 'label' => 'Pie', 'model' => $pie],
    ['slug' => 'donut', 'label' => 'Donut', 'model' => $donut],
    ['slug' => 'scatter', 'label' => 'Plot points', 'model' => $scatter],
    ['slug' => 'bubble', 'label' => 'Bubble points', 'model' => $bubble],
    ['slug' => 'diverging', 'label' => 'Diverging bars', 'model' => $diverging],
    ['slug' => 'population-pyramid', 'label' => 'Population pyramid', 'model' => $pyramid],
    ['slug' => 'sparkline', 'label' => 'Sparkline', 'model' => $sparkline],
    ['slug' => 'activity', 'label' => 'Activity', 'model' => $activity],
    ['slug' => 'activity-strip', 'label' => 'Activity strip', 'model' => $activityStrip],
    ['slug' => 'strip', 'label' => 'Strip', 'model' => $strip],
    ['slug' => 'stacked-incidents', 'label' => 'Stacked incidents', 'model' => $incidents],
];

$cards = '';

foreach ($galleryCharts as $galleryChart) {
    $views = '';

    foreach ($galleryThemes as $themeName => $galleryTheme) {
        $hidden = 'default' === $themeName ? '' : ' hidden';
        $svg = Chart::render($galleryChart['model'], $galleryTheme);
        $views .= \sprintf(
            '<div class="theme-view" data-theme-view="%s"%s>%s</div>',
            $themeName,
            $hidden,
            $svg,
        );
    }

    $compactClass = 'sparkline' === $galleryChart['slug'] ? ' chart-card--compact' : '';
    $cards .= \sprintf(
        '<article class="chart-card%s"><header class="card-meta"><span>%s</span><code>%s</code></header>%s</article>',
        $compactClass,
        $galleryChart['label'],
        $galleryChart['slug'],
        $views,
    );
}

$gallery = <<<HTML
<!doctype html>
<html lang="en" data-theme="default">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atelier Chart gallery</title>
    <style>
        :root {
            color-scheme: dark;
            --page-background: #05070d;
            --page-text: #e5e8ed;
            --page-muted: #b8bec8;
            --surface: #010205;
            --control: #0b1016;
            --control-active: #48c5ff;
            --control-active-text: #010205;
            --shadow: 0 0 0 1px #1b1f26;
            --shadow-hover: 0 0 0 1px #303338, 0 18px 44px rgb(0 0 0 / 28%);
        }
        :root[data-theme="dark"] {
            color-scheme: dark;
            --page-background: #101219;
            --page-text: #f5f7fb;
            --page-muted: #aab2c3;
            --surface: #151821;
            --control: #1e2230;
            --control-active: #8b9cff;
            --control-active-text: #101219;
            --shadow: 0 0 0 1px rgb(255 255 255 / 8%);
            --shadow-hover: 0 0 0 1px rgb(255 255 255 / 13%);
        }
        :root[data-theme="alto"] {
            color-scheme: dark;
            --page-background: #000000;
            --page-text: #ffffff;
            --page-muted: #888888;
            --surface: #050505;
            --control: #111111;
            --control-active: #8cc63f;
            --control-active-text: #050505;
            --shadow: 0 0 0 1px #222222;
            --shadow-hover: 0 0 0 1px #333333;
        }
        :root[data-theme="mono"] {
            --page-background: #ededed;
            --page-text: #161616;
            --page-muted: #666666;
            --surface: #ffffff;
            --control: #ffffff;
            --control-active: #1f2937;
            --control-active-text: #ffffff;
        }
        :root[data-theme="warm"] {
            --page-background: #f4ede5;
            --page-text: #352a24;
            --page-muted: #786a61;
            --surface: #fffaf4;
            --control: #fffaf4;
            --control-active: #d46345;
            --control-active-text: #ffffff;
        }
        * { box-sizing: border-box; }
        html { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        body {
            margin: 0;
            min-width: 320px;
            padding: clamp(20px, 4vw, 56px);
            background: var(--page-background);
            color: var(--page-text);
            font: 15px/1.5 Inter, ui-sans-serif, system-ui, sans-serif;
            transition: background-color 180ms ease-out, color 180ms ease-out;
        }
        .shell { width: min(1320px, 100%); margin: 0 auto; }
        .page-header { display: flex; align-items: end; justify-content: space-between; gap: 32px; margin-bottom: 32px; }
        .eyebrow { margin: 0 0 7px; color: var(--page-muted); font-size: 12px; font-weight: 650; letter-spacing: .12em; text-transform: uppercase; }
        h1 { margin: 0; font-size: clamp(30px, 4vw, 46px); line-height: 1.05; letter-spacing: -.04em; text-wrap: balance; }
        .intro { max-width: 620px; margin: 12px 0 0; color: var(--page-muted); font-size: 16px; text-wrap: pretty; }
        .theme-switcher {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 4px;
            padding: 4px;
            border-radius: 14px;
            background: color-mix(in srgb, var(--control) 82%, transparent);
            box-shadow: 0 0 0 1px rgb(0 0 0 / 6%), 0 8px 24px rgb(31 41 55 / 8%);
        }
        .theme-switcher button {
            min-width: 82px;
            min-height: 42px;
            padding: 0 14px;
            border: 0;
            border-radius: 10px;
            background: transparent;
            color: var(--page-muted);
            font: inherit;
            font-size: 13px;
            font-weight: 650;
            cursor: pointer;
            transition: background-color 160ms ease-out, color 160ms ease-out, scale 120ms ease-out, box-shadow 160ms ease-out;
        }
        .theme-switcher button:hover { color: var(--page-text); }
        .theme-switcher button[aria-pressed="true"] { background: var(--control-active); color: var(--control-active-text); box-shadow: 0 1px 2px rgb(0 0 0 / 12%); }
        .theme-switcher button:active { scale: .96; }
        .theme-switcher button:focus-visible { outline: 2px solid var(--control-active); outline-offset: 2px; }
        .chart-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px; }
        .chart-card {
            overflow: hidden;
            border-radius: 18px;
            background: var(--surface);
            box-shadow: var(--shadow);
            transition: box-shadow 160ms ease-out, transform 160ms ease-out;
        }
        .chart-card:hover { box-shadow: var(--shadow-hover); transform: translateY(-2px); }
        .card-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 46px;
            padding: 0 18px;
            border-bottom: 1px solid color-mix(in srgb, var(--page-text) 8%, transparent);
            color: var(--page-muted);
            font-size: 12px;
            font-weight: 650;
            letter-spacing: .02em;
        }
        .card-meta code { color: inherit; font: 500 11px/1 ui-monospace, SFMono-Regular, Menlo, monospace; opacity: .72; }
        .theme-view[hidden] { display: none; }
        .theme-view svg { display: block; width: 100%; height: auto; }
        .theme-view .atelier-chart__tick-label,
        .theme-view .atelier-chart__value-label,
        .theme-view .atelier-chart__gauge-label,
        .theme-view .atelier-chart__gauge-bound { font-variant-numeric: tabular-nums; }
        .chart-card--compact .theme-view { padding: 34px; }
        .chart-card--compact .theme-view svg { border-radius: 10px; }
        @media (max-width: 900px) {
            .page-header { align-items: start; flex-direction: column; }
            .theme-switcher { width: 100%; }
            .theme-switcher button { min-width: 0; }
        }
        @media (max-width: 720px) {
            body { padding: 18px; }
            .chart-grid { grid-template-columns: 1fr; gap: 18px; }
            .page-header { margin-bottom: 24px; }
        }
        @media (max-width: 440px) {
            .theme-switcher { grid-template-columns: repeat(2, 1fr); }
            .chart-card { border-radius: 14px; }
            .chart-card--compact .theme-view { padding: 24px; }
        }
        @media (prefers-reduced-motion: reduce) {
            body, .theme-switcher button, .chart-card { transition: none; }
            .chart-card:hover { transform: none; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="page-header">
            <div>
                <p class="eyebrow">Atelier SVG</p>
                <h1>Chart primitives</h1>
                <p class="intro">Typed PHP models, deterministic layout, and accessible SVG output. Switch themes to review every family against the same visual roles.</p>
            </div>
            <div class="theme-switcher" role="group" aria-label="Chart theme">
                <button type="button" data-theme-choice="default" aria-pressed="true">Atelier</button>
                <button type="button" data-theme-choice="alto" aria-pressed="false">Alto</button>
                <button type="button" data-theme-choice="dark" aria-pressed="false">Dark</button>
                <button type="button" data-theme-choice="mono" aria-pressed="false">Mono</button>
                <button type="button" data-theme-choice="warm" aria-pressed="false">Warm</button>
            </div>
        </header>
        <main class="chart-grid">{$cards}</main>
    </div>
    <script>
        const choices = [...document.querySelectorAll('[data-theme-choice]')];
        const views = [...document.querySelectorAll('[data-theme-view]')];

        function selectTheme(theme) {
            document.documentElement.dataset.theme = theme;

            for (const choice of choices) {
                choice.setAttribute('aria-pressed', String(choice.dataset.themeChoice === theme));
            }

            for (const view of views) {
                view.hidden = view.dataset.themeView !== theme;
            }
        }

        for (const choice of choices) {
            choice.addEventListener('click', () => selectTheme(choice.dataset.themeChoice));
        }
    </script>
</body>
</html>
HTML;

if (false === file_put_contents($output.'/index.html', $gallery)) {
    throw new RuntimeException('Unable to write gallery.');
}
