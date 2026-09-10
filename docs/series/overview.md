---
title: Series
description: Organize named values, points, slices, and comparisons before rendering a chart.
order: 10
---

# Series

A series groups related observations under one name. Add several series when the
reader needs to compare the same measure across groups.

<figure class="product-output product-output--chart">
<img src="../images/bar.svg" alt="Quarterly revenue grouped into two yearly series">
<figcaption>Each color represents one series across a shared set of categories.</figcaption>
</figure>

## Add series

Call `series()` once for each group. The builder preserves insertion order, so the
first series receives the first theme color and appears first in the legend.

```php
use Atelier\Chart\Chart;

$model = Chart::line()
    ->title('Weekly signups')
    ->series('Organic', [
        'Mon' => 12,
        'Tue' => 18,
        'Wed' => 16,
        'Thu' => 25,
        'Fri' => 31,
    ])
    ->series('Referral', [
        'Mon' => 8,
        'Tue' => 11,
        'Wed' => 14,
        'Thu' => 13,
        'Fri' => 19,
    ])
    ->build();
```

The builder validates the friendly array input and creates immutable series models.
The renderer reads those models but does not change them.

## Data shapes

| Chart family | Builder input | Multiple groups |
| --- | --- | --- |
| Bar, line, area, radar | Named category-value series | Yes |
| Stacked bar | Named category-value series | Yes |
| Diverging bar | Two named magnitude series | Exactly two |
| Scatter, bubble | Named lists of points | Yes |
| Gauge | One primary value and optional comparisons | Up to three values total |
| Pie, donut | Named slices | Slices, not series |
| Sparkline | One ordered value list | No |
| Activity | One named category-value series | No |
| Strip | An ordered list of weighted, labeled segments | Segments, not series |

Use [Categories](categories.md) for values keyed by discrete labels, [Points](points.md)
for numeric coordinates, and [Composition](composition.md) for stacks, slices,
diverging pairs, and gauge comparisons.

## Colors

Pass a color as the final argument to `series()` when a series has a fixed semantic
color. Without an explicit color, the renderer selects the next color from the active
theme.

```php
$model = Chart::bar()
    ->series('Actual', ['Q1' => 18, 'Q2' => 27], '#2563eb')
    ->series('Forecast', ['Q1' => 21, 'Q2' => 31], '#94a3b8')
    ->build();
```

Series names and explicit colors must not be blank. Numeric values must be finite.
Omit a color or pass `null` to use the theme palette. Chart-specific builders add
stricter rules before they return a model.
