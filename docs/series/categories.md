---
title: Categories
description: Keep category labels and ordering consistent across cartesian series.
order: 20
---

# Categories

Categories identify discrete positions such as quarters, regions, or product names.
Use associative arrays so each value carries its label.

<figure class="product-output product-output--chart">
<img src="../images/line.svg" alt="Two line series sharing the same ordered categories">
<figcaption>Both series are evaluated against one shared category sequence.</figcaption>
</figure>

## Shared order

The first call to `series()` defines the category labels and their order. Every later
series must provide the same keys in the same order.

```php
$model = Chart::bar()
    ->series('2025', ['Q1' => 18, 'Q2' => 32, 'Q3' => 24])
    ->series('2026', ['Q1' => 26, 'Q2' => 24, 'Q3' => 38])
    ->build();
```

This mismatch is rejected even though it contains the same labels:

```php
Chart::bar()
    ->series('2025', ['Q1' => 18, 'Q2' => 32])
    ->series('2026', ['Q2' => 24, 'Q1' => 26]);
```

Stable ordering keeps each value attached to the intended visual position. Category
labels must also be unique and non-empty.

## Values

Category values must be finite integers or floats. Grouped bar, line, and area charts
accept positive and negative values. Stacked bars require non-negative values because
each segment contributes to a total.

Radar charts also require non-negative values and at least three categories. The
renderer places categories around the radial frame in insertion order.

## Positioning

The renderer divides the available plot into equal category bands. Labels and marks
are centered within those bands automatically. There is no public API for individual
category positions or label alignment.
