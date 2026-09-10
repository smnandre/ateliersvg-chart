---
title: Scales and layout
description: Understand how chart values become positioned, labelled, accessible SVG geometry.
order: 10
---

# Scales and layout

Scales map validated values into coordinates. Layout allocates space for the title,
legend, plot, axes, and labels before the renderer constructs the SVG document.

<figure class="product-output product-output--chart">
<img src="../images/bar.svg" alt="Grouped bars with title, legend, axes, labels, and plot marks">
<figcaption>The renderer places every visual element from the model, dimensions, and theme.</figcaption>
</figure>

## Render SVG

Build a model, then pass it directly to `Chart::render()`.

```php
use Atelier\Chart\Chart;

$model = Chart::bar()
    ->title('Quarterly revenue')
    ->description('Revenue by quarter for 2025 and 2026, in millions.')
    ->series('2025', ['Q1' => 18, 'Q2' => 32, 'Q3' => 24, 'Q4' => 42])
    ->series('2026', ['Q1' => 26, 'Q2' => 24, 'Q3' => 38, 'Q4' => 35])
    ->build();

echo Chart::render($model);
```

Use `toSvgDocument()` when the result must remain an Atelier SVG document for further
composition.

```php
$document = Chart::renderDocument($model);
```

## Layout stages

1. The builder validates input and creates an immutable chart model.
2. Layout allocates the title, legend, plot, axes, and labels within the canvas.
3. Scales map data domains to positions and generate readable ticks.
4. The theme resolves background, text, grid, axis, and series colors.
5. The SVG renderer creates the document and semantic attributes.

The same model, size, and theme always produce the same SVG.

## Configurable behavior

The builders expose the decisions that belong to the chart semantics:

- `title()` and `description()` provide the accessible chart context.
- `size()` sets the coordinate space used for layout.
- `showValues()` controls value labels on supported chart types.
- `horizontal()` and `vertical()` set bar orientation where supported.
- `axes()` names the numeric axes on scatter and bubble charts.
- Explicit colors and themes set visual roles.

## Automatic behavior

Plot margins, tick intervals, zero baselines, mark positions, label alignment, and
legend placement are computed automatically. These internal layout decisions do not
have public setters.

Continue with [Scales](scales.md), [Legends](legends.md), [Labels](labels.md), and
[Sizing](sizing.md) for the behavior of each part.
