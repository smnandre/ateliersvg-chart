---
title: Composition
description: Model stacks, diverging pairs, slices, and gauge comparisons with the right data shape.
order: 40
---

# Composition

Composition charts express how values combine, oppose one another, or sit within a
bounded range. They use related data concepts, but not all of them use the same series
model.

<figure class="product-output product-output--chart">
<img src="../images/stacked-bar.svg" alt="Three non-negative series stacked within each category">
<figcaption>Stacked series retain their names while contributing to category totals.</figcaption>
</figure>

## Stacks

Stacked bars accept one or more named series. Every series uses the same categories,
the same order, and non-negative values. The renderer sums corresponding values to
compute each category total.

```php
$model = Chart::stackedBar()
    ->series('Product', ['Jan' => 42, 'Feb' => 36])
    ->series('Services', ['Jan' => 24, 'Feb' => 31])
    ->series('Partners', ['Jan' => 16, 'Feb' => 19])
    ->build();
```

## Diverging pairs

A diverging bar chart accepts exactly two series of non-negative magnitudes. The
renderer places the first series above or to the right of zero and the second below or
to the left.

```php
$model = Chart::divergingBar()
    ->horizontal()
    ->series('Women', ['0-9' => 18, '10-19' => 22])
    ->series('Men', ['0-9' => 20, '10-19' => 19])
    ->build();
```

Do not negate the second series. Direction is part of the chart model, not the input
data.

## Slices

Pie and donut charts use `slice()` rather than `series()`. Each slice has a unique
label and a positive value. The renderer divides each value by the total and displays
its percentage in the legend.

```php
$model = Chart::donut()
    ->slice('Direct', 42)
    ->slice('Search', 31)
    ->slice('Referral', 18)
    ->build();
```

## Gauge comparisons

`Chart::gauge($value)` creates the primary value. Add up to two comparison values with
`series()`. All values must fall within the configured range.

```php
$model = Chart::gauge(78)
    ->label('Overall')
    ->series('Quality', 86)
    ->series('Readiness', 71)
    ->range(0, 100)
    ->unit('%')
    ->build();
```

The renderer draws multiple gauge values as concentric arcs. Their order follows the
primary value, then the comparison series in insertion order.
