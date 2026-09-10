<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Internal\Input;

final readonly class StripChart implements ChartModel
{
    /** @param non-empty-list<StripSegment> $segments */
    public function __construct(
        public array $segments,
        private string $chartTitle = 'Strip',
        private ?string $chartDescription = null,
        public bool $showsLegend = true,
        private float $chartWidth = 720.0,
        private float $chartHeight = 240.0,
    ) {
        if ([] === $segments) {
            throw new InvalidArgumentException('Strip charts require at least one segment.');
        }
        if ('' === trim($chartTitle)) {
            throw new InvalidArgumentException('Strip chart titles must not be empty.');
        }
        if (null !== $chartDescription && '' === trim($chartDescription)) {
            throw new InvalidArgumentException('Strip chart descriptions must not be empty when provided.');
        }
        Input::dimensions($chartWidth, $chartHeight, 320.0, 160.0, 'Strip charts');
        $this->totalWeight();
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

    public function totalWeight(): float
    {
        return Input::finiteSum(array_map(static fn (StripSegment $segment): float => $segment->weight, $this->segments), 'Strip segments');
    }
}
