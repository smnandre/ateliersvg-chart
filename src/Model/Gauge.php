<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Internal\Input;

final readonly class Gauge implements ChartModel
{
    /**
     * @param list<GaugeSeries> $comparisons
     */
    public function __construct(
        public float $value,
        public float $minimum,
        public float $maximum,
        public string $unit = '',
        public ?string $color = null,
        private string $chartTitle = 'Gauge chart',
        private ?string $chartDescription = null,
        private float $chartWidth = 360.0,
        private float $chartHeight = 220.0,
        public string $label = 'Current',
        public array $comparisons = [],
    ) {
        if (!is_finite($value) || !is_finite($minimum) || !is_finite($maximum)) {
            throw new InvalidArgumentException('Gauge values and bounds must be finite numbers.');
        }
        if ($minimum >= $maximum) {
            throw new InvalidArgumentException('Gauge minimum must be lower than its maximum.');
        }
        if (!is_finite($maximum - $minimum)) {
            throw new InvalidArgumentException('Gauge range must have a finite span.');
        }
        if ($value < $minimum || $value > $maximum) {
            throw new InvalidArgumentException('Gauge value must be inside its range.');
        }
        if (null !== $color && '' === trim($color)) {
            throw new InvalidArgumentException('Gauge color must not be empty when provided.');
        }
        if ('' === trim($label)) {
            throw new InvalidArgumentException('Gauge labels must not be empty.');
        }
        if (2 < count($comparisons)) {
            throw new InvalidArgumentException('A gauge supports at most three series.');
        }
        foreach ($comparisons as $comparison) {
            if ($comparison->value < $minimum || $comparison->value > $maximum) {
                throw new InvalidArgumentException('Gauge series values must be inside the gauge range.');
            }
        }
        if ('' === trim($chartTitle)) {
            throw new InvalidArgumentException('Gauge title must not be empty.');
        }
        if (null !== $chartDescription && '' === trim($chartDescription)) {
            throw new InvalidArgumentException('Gauge description must not be empty when provided.');
        }
        Input::dimensions($chartWidth, $chartHeight, 240.0, 160.0, 'Gauges');
    }

    public function width(): float
    {
        return $this->chartWidth;
    }

    public function height(): float
    {
        return $this->chartHeight;
    }

    public function title(): string
    {
        return $this->chartTitle;
    }

    public function description(): ?string
    {
        return $this->chartDescription;
    }

    public function ratio(): float
    {
        return ($this->value - $this->minimum) / ($this->maximum - $this->minimum);
    }

    /**
     * @return non-empty-list<GaugeSeries>
     */
    public function series(): array
    {
        return [new GaugeSeries($this->label, $this->value, $this->color), ...$this->comparisons];
    }

    public function ratioFor(GaugeSeries $series): float
    {
        return ($series->value - $this->minimum) / ($this->maximum - $this->minimum);
    }
}
