<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

interface ChartModel
{
    public function width(): float;

    public function height(): float;

    public function title(): string;

    public function description(): ?string;
}
