<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Internal\Input;

final readonly class Sparkline implements ChartModel
{
    /**
     * @param non-empty-list<float> $values
     */
    public function __construct(
        public array $values,
        private string $chartTitle = 'Sparkline chart',
        private ?string $chartDescription = null,
        private float $chartWidth = 240.0,
        private float $chartHeight = 64.0,
        public ?string $color = null,
    ) {
        if (count($values) < 2) {
            throw new InvalidArgumentException('A sparkline requires at least two values.');
        }
        foreach ($values as $value) {
            if (!is_finite($value)) {
                throw new InvalidArgumentException('Sparkline values must be finite numbers.');
            }
        }
        if ('' === trim($chartTitle)) {
            throw new InvalidArgumentException('Sparkline title must not be empty.');
        }
        if (null !== $chartDescription && '' === trim($chartDescription)) {
            throw new InvalidArgumentException('Sparkline description must not be empty when provided.');
        }
        Input::dimensions($chartWidth, $chartHeight, 80.0, 24.0, 'Sparklines');
        if (null !== $color && '' === trim($color)) {
            throw new InvalidArgumentException('Sparkline color must not be empty when provided.');
        }
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
}
