---
title: Points
description: Define scatter and bubble series with numeric coordinates and optional sizes.
order: 30
---

# Points

Scatter and bubble charts use numeric coordinates instead of named categories. Each
series contains its own list of points, and every series shares the same two axes.

<figure class="product-output product-output--chart">
<img src="../images/scatter.svg" alt="Two point series plotted on shared numeric axes">
<figcaption>Each group can contain different coordinates while sharing the same scales.</figcaption>
</figure>

## Scatter series

Each scatter point requires finite `x` and `y` values.

```php
$model = Chart::scatter()
    ->title('Customer distribution')
    ->axes('Age', 'Annual spend')
    ->series('New', [
        ['x' => 22, 'y' => 28],
        ['x' => 31, 'y' => 41],
        ['x' => 45, 'y' => 58],
    ])
    ->series('Returning', [
        ['x' => 25, 'y' => 82],
        ['x' => 43, 'y' => 55],
    ])
    ->build();
```

Unlike category series, point series do not need matching coordinates or the same
number of points.

## Bubble series

Bubble points also require a positive `size`. The renderer maps the square root of
that value to the radius, so the visible area remains proportional to the supplied
size.

```php
$model = Chart::bubble()
    ->title('Market opportunities')
    ->axes('Growth %', 'Margin %')
    ->series('Markets', [
        ['x' => 18, 'y' => 62, 'size' => 420],
        ['x' => 28, 'y' => 54, 'size' => 270],
        ['x' => 48, 'y' => 38, 'size' => 120],
    ])
    ->build();
```

The largest size in the complete chart establishes the bubble scale. Point order does
not define a line or imply continuity.

## SVG data

With `SvgRenderOptions(dataAttributes: true)`, every rendered point exposes `data-series`, `data-x`, `data-y`, and `data-size`.
These attributes support inspection or progressive enhancement without making hover
the only way to read essential chart information.
