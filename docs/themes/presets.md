---
title: Presets
description: Compare the five included chart themes and select one at render time.
order: 20
---

# Presets

Atelier Chart includes five immutable theme presets. Each preset defines the complete
set of visual roles required by the renderer.

<div class="figure-grid">
<figure class="product-output product-output--chart">
<img src="../images/theme-default.svg" alt="Grouped bar chart in the default theme">
<figcaption>Default</figcaption>
</figure>
<figure class="product-output product-output--chart">
<img src="../images/theme-alto.svg" alt="Grouped bar chart in the Alto theme">
<figcaption>Alto</figcaption>
</figure>
<figure class="product-output product-output--chart">
<img src="../images/theme-dark.svg" alt="Grouped bar chart in the dark theme">
<figcaption>Dark</figcaption>
</figure>
<figure class="product-output product-output--chart">
<img src="../images/theme-mono.svg" alt="Grouped bar chart in the monochrome theme">
<figcaption>Mono</figcaption>
</figure>
<figure class="product-output product-output--chart">
<img src="../images/theme-warm.svg" alt="Grouped bar chart in the warm theme">
<figcaption>Warm</figcaption>
</figure>
</div>

## Available presets

| Preset | Intended surface |
| --- | --- |
| `Theme::default()` | Near-black Atelier canvas with a cyan-led palette |
| `Theme::alto()` | Black canvas with the original Alto accent scale |
| `Theme::dark()` | Soft dark canvas with lighter text and muted accents |
| `Theme::mono()` | White canvas with a grayscale palette |
| `Theme::warm()` | Warm light canvas with earthy accents |

Select the preset when rendering, not when building the model.

```php
$svg = Chart::render($model, Theme::mono());
```

This separation lets one immutable chart model produce several presentation variants.
The selected preset is resolved directly into SVG attributes, so the output does not
depend on page-level styles.
