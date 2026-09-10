---
title: Getting started
description: Install Atelier Chart, render a complete SVG chart, and choose the right builder for your data.
order: 10
---

# Getting started

Atelier Chart turns validated PHP data into accessible SVG. Choose a builder, create
an immutable model, then render that model as a string or an Atelier SVG document.

## Install

Atelier Chart requires PHP 8.3 or later.

```bash
composer require atelier/chart
```

## Render a chart

Create a PHP file with a grouped bar chart and write the rendered SVG to disk.

```php
<?php

declare(strict_types=1);

use Atelier\Chart\Chart;

require __DIR__.'/vendor/autoload.php';

$model = Chart::bar()
    ->title('Quarterly revenue')
    ->description('Revenue by quarter for 2025 and 2026, in millions.')
    ->series('2025', ['Q1' => 18, 'Q2' => 32, 'Q3' => 24, 'Q4' => 42])
    ->series('2026', ['Q1' => 26, 'Q2' => 24, 'Q3' => 38, 'Q4' => 35])
    ->build();

$svg = Chart::render($model);

file_put_contents(__DIR__.'/revenue.svg', $svg);
```

Open `revenue.svg` in a browser. The result contains a visible title, legend, axes,
values, and two series of grouped bars.

<figure class="product-output product-output--chart">
<img src="images/bar.svg" alt="Quarterly revenue for 2025 and 2026 rendered as grouped bars">
<figcaption>The complete output from a builder, an immutable model, and the default theme.</figcaption>
</figure>

The example has three stages:

1. `Chart::bar()` selects the chart family and returns its builder.
2. `build()` validates the input and returns an immutable chart model.
3. `Chart::render($model)` renders that model as an SVG string.

## Choose a chart

Start with the relationship the reader needs to see.

| Chart | Start with | Use for |
| --- | --- | --- |
| [Bar](charts/bar.md) | `Chart::bar()` | Comparing values across categories |
| [Line](charts/line.md) | `Chart::line()` | Showing change through an ordered sequence |
| [Area](charts/area.md) | `Chart::area()` | Emphasizing magnitude through an ordered sequence |
| [Radar](charts/radar.md) | `Chart::radar()` | Comparing multivariate profiles |
| [Pie](charts/pie.md) | `Chart::pie()` | Showing a small set of parts within a whole |
| [Donut](charts/donut.md) | `Chart::donut()` | Showing composition with a central total |
| [Scatter](charts/scatter.md) | `Chart::scatter()` | Finding relationships between two numeric variables |
| [Bubble](charts/bubble.md) | `Chart::bubble()` | Adding a third numeric variable as area |
| [Stacked bar](charts/stacked-bar.md) | `Chart::stackedBar()` | Comparing totals and their composition |
| [Diverging bar](charts/diverging-bar.md) | `Chart::divergingBar()` | Comparing paired magnitudes around zero |
| [Gauge](charts/gauge.md) | `Chart::gauge($value)` | Showing values within an explicit range |
| [Sparkline](charts/sparkline.md) | `Chart::sparkline($values)` | Embedding a compact trend without full axes |
| [Activity](charts/activity.md) | `Chart::activity()` | Showing intensity across equal periods |
| [Strip](charts/strip.md) | `Chart::strip()` | Showing an ordered sequence of segments with proportional weights |

## Change the output

Pass a theme at render time without rebuilding the model.

```php
use Atelier\Chart\Theme\Theme;

$svg = Chart::render($model, Theme::mono());
```

Use `size()` before `build()` when the chart needs a different layout coordinate
space.

```php
$model = Chart::bar()
    ->size(480, 320)
    ->series('Revenue', ['Q1' => 18, 'Q2' => 32, 'Q3' => 24])
    ->build();
```

Use `renderDocument()` when the result must remain an Atelier SVG document for further
composition.

```php
$document = Chart::renderDocument($model);
```

## Include CSS and data hooks

The default SVG uses presentation attributes and inherited text styles. It has no
CSS classes or `data-*` attributes. The chart title, description, data summary, and
accessibility attributes remain present.

Enable hooks when your page needs to target chart elements with CSS or JavaScript:

```php
use Atelier\Chart\Renderer\Svg\SvgRenderOptions;

$svg = Chart::render($model, options: new SvgRenderOptions(
    classes: true,
    dataAttributes: true,
));
```

| Option | Default | Effect |
|---|---|---|
| `classes` | `false` | Adds `atelier-chart` and element classes such as `atelier-chart__tick-label` |
| `dataAttributes` | `false` | Adds `data-renderer` and chart-specific values such as `data-series`, `data-category`, and `data-value` |

The options are independent. Pass the same `options` argument to `renderDocument()`,
or to the `SvgRenderer` constructor when using the renderer directly.

Existing CSS or JavaScript that targets these hooks must enable the relevant option.
Text styles may be inherited from the root or a group; use computed styles when
inspecting appearance. Coordinates and paint order are preserved.

## Handle invalid input

Builders reject incomplete or incompatible data before returning a model. Package
validation errors implement `ExceptionInterface`.

```php
use Atelier\Chart\Exception\ExceptionInterface;

try {
    $model = Chart::bar()
        ->series('Actual', ['Q1' => 18, 'Q2' => 32])
        ->series('Forecast', ['Q2' => 29, 'Q1' => 21])
        ->build();
} catch (ExceptionInterface $exception) {
    echo $exception->getMessage();
}
```

Here the category order differs between series, so the builder reports the mismatch.
Runtime values are checked before conversion, so malformed arrays produce package
exceptions instead of PHP warnings or silent numeric coercion.

## Format displayed values

When `dataAttributes` is enabled, `data-value`, `data-x`, and related attributes preserve
the model's floating-point value for software, independently of PHP's `serialize_precision` setting.
All numeric input becomes a PHP float. Integers above 2^53 and arbitrary-precision decimals
may lose precision during input conversion. SVG coordinates are rounded to two decimal
places, so very small marks can disappear; choose units and canvas dimensions accordingly.
Visible ticks and labels use a `ValueFormatterInterface`, which can be supplied at render
time without changing geometry.

```php
use Atelier\Chart\Formatter\ValueFormatterInterface;

$currency = new class implements ValueFormatterInterface {
    public function format(float $value): string
    {
        return '$'.number_format($value, 2);
    }
};

$svg = Chart::render($model, formatter: $currency);
```

Every SVG includes a native title and a generated description of its data. The root uses
`aria-label` instead of generated IDs, so multiple charts can be embedded in one document
without accessibility ID collisions.

## Next steps

- Learn how named values and points are organized in [Series](series/overview.md).
- See how values become geometry in [Scales and layout](layout/overview.md).
- Select a preset or define visual roles in [Themes](themes/overview.md).
