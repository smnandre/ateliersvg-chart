---
title: Legends
description: Understand when legends appear and how their order, labels, colors, and placement are resolved.
order: 30
---

# Legends

Legends connect rendered colors to series or slices. Their content follows the model
order and their placement follows the chart family and canvas size.

<figure class="product-output product-output--chart">
<img src="../images/line.svg" alt="Two line series identified by a legend above the plot">
<figcaption>Series names and colors are repeated in insertion order above the plot.</figcaption>
</figure>

## Series legends

Grouped bar, line, area, radar, stacked bar, scatter, and bubble charts render a
legend when they contain more than one series. A single series needs no separate key
because its name does not distinguish it from another group.

Diverging bars always contain exactly two series and therefore always render a legend.
Gauges add a comparison legend when more than one value is present.

```php
$model = Chart::line()
    ->series('Actual', ['Jan' => 18, 'Feb' => 27])
    ->series('Forecast', ['Jan' => 21, 'Feb' => 31])
    ->build();
```

The first item describes the first `series()` call. Series with an explicit color keep
that color; the rest use the active theme palette in order.

## Slice legends

Pie and donut charts always render one legend item per slice. Each item contains the
slice label and its calculated percentage.

On canvases at least 520 pixels wide, the legend appears beside the chart. On narrower
canvases, it moves below the chart and uses two columns. This placement is automatic.

## Space

Cartesian legend items wrap to additional rows, and the plot reserves the corresponding
vertical space. Pie legends adapt between side and two-column layouts. When labels or the
number of entries cannot fit the requested dimensions, rendering throws a package
`InvalidArgumentException` with a size-specific message instead of clipping the output.

Strip charts show one legend entry per distinct label and resolved color pair. Use
`withoutLegend()` on the strip builder to hide that legend. Other families choose legend
visibility and placement in the renderer; there is no general legend configuration object.
