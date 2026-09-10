<?php

declare(strict_types=1);

namespace Atelier\Chart\Builder;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\StripChart;
use Atelier\Chart\Model\StripSegment;

final class StripChartBuilder
{
    /** @var list<StripSegment> */
    private array $segments = [];

    private string $title = 'Strip';
    private ?string $description = null;
    private bool $legend = true;
    private float $width = 720.0;
    private float $height = 240.0;

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function size(float $width, float $height): self
    {
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    public function withoutLegend(): self
    {
        $this->legend = false;

        return $this;
    }

    /** Appends one segment; only the relative weight determines its width. */
    public function segment(float $weight, string $label, ?string $color = null): self
    {
        $this->segments[] = new StripSegment($weight, $label, $color);

        return $this;
    }

    public function build(): StripChart
    {
        if ([] === $this->segments) {
            throw new InvalidArgumentException('Add at least one segment before building a strip chart.');
        }

        return new StripChart(
            $this->segments,
            $this->title,
            $this->description,
            $this->legend,
            $this->width,
            $this->height,
        );
    }
}
