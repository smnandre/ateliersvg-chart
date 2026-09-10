<?php

declare(strict_types=1);

namespace Atelier\Chart\Builder;

use Atelier\Chart\Model\Gauge;
use Atelier\Chart\Model\GaugeSeries;

final class GaugeBuilder
{
    private float $minimum = 0.0;
    private float $maximum = 100.0;
    private string $unit = '';
    private ?string $color = null;
    private string $title = 'Gauge chart';
    private ?string $description = null;
    private float $width = 360.0;
    private float $height = 220.0;
    private string $label = 'Current';

    /** @var list<GaugeSeries> */
    private array $comparisons = [];

    public function __construct(private readonly float $value)
    {
    }

    public function range(float $minimum, float $maximum): self
    {
        $this->minimum = $minimum;
        $this->maximum = $maximum;

        return $this;
    }

    public function unit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function series(string $name, float $value, ?string $color = null): self
    {
        $this->comparisons[] = new GaugeSeries($name, $value, $color);

        return $this;
    }

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

    public function build(): Gauge
    {
        return new Gauge(
            $this->value,
            $this->minimum,
            $this->maximum,
            $this->unit,
            $this->color,
            $this->title,
            $this->description,
            $this->width,
            $this->height,
            $this->label,
            $this->comparisons,
        );
    }
}
