---
title: Scales
description: See how values become positions, axes, ticks, ratios, and bounded arcs.
order: 20
---

# Scales

A scale maps values from a data domain into the chart's coordinate space. Atelier
Chart computes domains and tick intervals from the complete model.

<figure class="product-output product-output--chart">
<img src="../images/scatter.svg" alt="Scatter plot with independently scaled horizontal and vertical axes">
<figcaption>Point charts derive one linear scale for each numeric axis.</figcaption>
</figure>

## Linear scales

Grouped bars, lines, areas, and point charts use linear scales. The renderer:

1. Finds the minimum and maximum across all relevant series.
2. Includes zero for bars, areas, radar charts, stacks, and diverging charts.
3. Fits line, scatter, bubble, and sparkline domains to their data by default.
4. Expands equal minimum and maximum values into a usable domain.
5. Chooses a readable tick step based on powers of ten and factors of 1, 2, 5, or 10.
6. Rounds automatic domains outward to complete tick boundaries.

This behavior keeps multiple series comparable because they share the same scale.
Override the automatic policy with `includeZero()`. Use `domain()` on cartesian builders,
or `xDomain()` and `yDomain()` on point builders, when charts must share exact bounds.

```php
$line = Chart::line()
    ->domain(0, 100)
    ->series('Completion', ['Mon' => 42, 'Tue' => 68])
    ->build();

$scatter = Chart::scatter()
    ->xDomain(1_000, 1_100)
    ->yDomain(0, 50)
    ->series('Samples', [['x' => 1_025, 'y' => 18]])
    ->build();
```

Fixed domains remain exact. Values outside them are rejected rather than drawn outside the
plot. If a bar or area domain excludes zero, its baseline is the nearest domain edge;
this truncates the displayed magnitude, so make the chosen bounds clear to readers.
Domains and ranges whose spans cannot be represented as finite floats are rejected.
Logarithmic scales are not currently exposed.

## Axes

Cartesian charts derive one numeric axis and one category axis. Scatter and bubble
charts derive independent horizontal and vertical numeric axes. `axes()` adds names to
the point-chart axes; it does not change their domains.

```php
$model = Chart::scatter()
    ->axes('Age', 'Annual spend')
    ->series('Customers', [
        ['x' => 22, 'y' => 28],
        ['x' => 45, 'y' => 58],
    ])
    ->build();
```

Zero ticks use the axis color and a stronger stroke. Other ticks use the grid color.
Tick labels use compact automatic formatting unless rendering receives a custom
`ValueFormatterInterface`.

## Specialized domains

- Stacked bars scale against category totals rather than individual segments.
- Diverging bars scale both sides from zero to the largest supplied magnitude.
- Radar charts use a shared non-negative radial domain across every series.
- Sparklines fit their local minimum and maximum without forcing zero into the domain.
- Pie and donut charts divide each positive slice by the total.
- Gauges map every value into the explicit range set with `range()`.
- Activity cells encode relative intensity with opacity; strip segments encode relative weight with width.

Choose the chart family whose scale semantics match the question. Changing a theme
changes visual roles, not domains or positions.
