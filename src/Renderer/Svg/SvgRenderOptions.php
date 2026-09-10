<?php

declare(strict_types=1);

namespace Atelier\Chart\Renderer\Svg;

final readonly class SvgRenderOptions
{
    public function __construct(
        public bool $classes = false,
        public bool $dataAttributes = false,
    ) {
    }
}
