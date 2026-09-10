---
title: Custom themes
description: Define explicit colors, a palette, and typography for a chart system.
order: 30
---

# Custom themes

Create a `Theme` when the included presets do not match the product or publication.
Every visual role is explicit, so custom output remains deterministic.

```php
use Atelier\Chart\Chart;
use Atelier\Chart\Theme\Theme;

$theme = new Theme(
    backgroundColor: '#ffffff',
    textColor: '#172033',
    mutedTextColor: '#607089',
    gridColor: '#e7ebf0',
    axisColor: '#9aa5b5',
    palette: ['#2563eb', '#e11d48', '#0f9f6e', '#d97706'],
    fontFamily: 'Inter, ui-sans-serif, system-ui, sans-serif',
);

echo Chart::render($model, $theme);
```

## Required roles

| Constructor argument | Renderer use |
| --- | --- |
| `backgroundColor` | SVG canvas and contrasting mark details |
| `textColor` | Titles, primary values, and important labels |
| `mutedTextColor` | Ticks, categories, legends, and secondary labels |
| `gridColor` | Grid lines, gauge tracks, and sparkline guides |
| `axisColor` | Axes, zero baselines, and radar spokes |
| `palette` | Series, slices, bubbles, arcs, strip segments, and sparkline marks |
| `fontFamily` | Every text element emitted by the renderer |

The palette must contain at least one color. Color strings and the font family must
not be empty. Atelier Chart writes color values into SVG but does not otherwise parse
or normalize CSS color syntax.

## Derive a preset

Use `with()` when a preset is already close to the required system. Unspecified visual
roles remain unchanged.

```php
$theme = Theme::warm()->with(
    textColor: '#241b16',
    palette: ['#b84b35', '#236d68', '#b98222'],
);
```

## Palette order

The first data group uses palette index zero, the second index one, and so on. The
palette cycles when the number of groups exceeds its length.

Use explicit colors on individual series only when the color carries stable meaning.
For ordinary differentiation, keep colors in the theme so charts remain consistent
and can switch presentation without rebuilding their models.

For strips, palette order follows the first appearance of each distinct segment label.
An explicit segment color overrides that segment only.
