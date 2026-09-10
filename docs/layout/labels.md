---
title: Labels
description: Place accessible descriptions and supported value labels without relying on hover.
order: 40
---

# Labels

Atelier Chart keeps essential information in the SVG. Titles, descriptions, axis
labels, and value labels do not require JavaScript or hover.

<figure class="product-output product-output--chart">
<img src="../images/bar.svg" alt="Grouped bar chart with visible values above each bar">
<figcaption>Bar charts show value labels above the bars by default.</figcaption>
</figure>

## Accessible context

Every chart has a title. Add a description when the title alone does not communicate
the subject, units, comparison, or time period.

```php
$model = Chart::bar()
    ->title('Quarterly revenue')
    ->description('Revenue by quarter for 2025 and 2026, in millions.')
    ->series('2025', ['Q1' => 18, 'Q2' => 32])
    ->series('2026', ['Q1' => 26, 'Q2' => 24])
    ->build();
```

The root SVG has `role="img"` and an `aria-label` containing the chart title. Its `<title>`
and `<desc>` preserve the title, optional description, and generated data summary.

## Value labels

Grouped bars show values by default. Line, area, radar, stacked bar, and diverging bar
charts hide them by default. Use `showValues()` or `showValues(false)` to change the
supported chart's setting.

```php
$model = Chart::stackedBar()
    ->showValues()
    ->series('Product', ['Jan' => 42, 'Feb' => 36])
    ->series('Services', ['Jan' => 24, 'Feb' => 31])
    ->build();
```

Stacked bars omit a value label when its segment is too small to contain the text. Pie
and donut legends always show calculated percentages. Gauges show the primary value,
range bounds, unit, and comparison values. Sparklines show first and last values when
their height is at least 48 pixels.

## Alignment

The renderer aligns tick, category, value, and radial labels from their geometry. This
includes selecting left, center, or right text anchors around a radar chart. Individual
alignment and offsets are not configurable.

## Data attributes

Enable machine-readable values when you need to inspect marks or add interactions:

```php
use Atelier\Chart\Renderer\Svg\SvgRenderOptions;

$svg = Chart::render($model, options: new SvgRenderOptions(dataAttributes: true));
```

With this option:

- Cartesian marks expose series, category, and value.
- Scatter and bubble points expose series, x, y, and size (1 for scatter points).
- Pie and donut slices expose label, value, and ratio.
- Activity cells expose series, category, value, and optional labels.
- Strip segments expose label, weight, and ratio.

Numeric attributes and the accessible summary preserve model floats independently of the
visible formatter and PHP's `serialize_precision` setting. They cannot recover precision
lost when input was converted to floats.

These attributes can support progressive enhancement, but accessible interpretation
should still come from the chart title, description, and visible labels.
