<?php

declare(strict_types=1);

namespace Atelier\Chart\Renderer;

use Atelier\Chart\Model\ChartModel;
use Atelier\Chart\Theme\Theme;
use Atelier\Svg\Document;

interface RendererInterface
{
    public function render(ChartModel $chart, Theme $theme): string;

    public function renderToDocument(ChartModel $chart, Theme $theme): Document;
}
