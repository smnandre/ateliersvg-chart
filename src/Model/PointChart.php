<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Internal\Input;
use Atelier\Chart\Scale\Domain;

final readonly class PointChart implements ChartModel
{
    public Domain $xDomain;
    public Domain $yDomain;

    /**
     * @param non-empty-list<PointSeries> $series
     */
    public function __construct(
        public PointChartKind $kind,
        public array $series,
        private string $chartTitle,
        private ?string $chartDescription = null,
        private float $chartWidth = 720.0,
        private float $chartHeight = 420.0,
        public ?string $xLabel = null,
        public ?string $yLabel = null,
        ?Domain $xDomain = null,
        ?Domain $yDomain = null,
    ) {
        $this->xDomain = $xDomain ?? Domain::automatic();
        $this->yDomain = $yDomain ?? Domain::automatic();
        if ([] === $series) {
            throw new InvalidArgumentException('Point charts require at least one series.');
        }
        if ('' === trim($chartTitle)) {
            throw new InvalidArgumentException('Point chart title must not be empty.');
        }
        if (null !== $chartDescription && '' === trim($chartDescription)) {
            throw new InvalidArgumentException('Point chart description must not be empty when provided.');
        }
        if (null !== $xLabel && '' === trim($xLabel)) {
            throw new InvalidArgumentException('The horizontal axis label must not be empty when provided.');
        }
        if (null !== $yLabel && '' === trim($yLabel)) {
            throw new InvalidArgumentException('The vertical axis label must not be empty when provided.');
        }
        Input::dimensions($chartWidth, $chartHeight, 320.0, 240.0, 'Point charts');
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

    /** @return non-empty-list<float> */
    public function xValues(): array
    {
        return $this->valuesForAxis(true);
    }

    /** @return non-empty-list<float> */
    public function yValues(): array
    {
        return $this->valuesForAxis(false);
    }

    /** @return non-empty-list<float> */
    public function sizes(): array
    {
        $values = [];

        foreach ($this->series as $series) {
            foreach ($series->points as $point) {
                $values[] = $point->size;
            }
        }

        return $values;
    }

    /** @return non-empty-list<float> */
    private function valuesForAxis(bool $horizontal): array
    {
        $values = [];

        foreach ($this->series as $series) {
            foreach ($series->points as $point) {
                $values[] = $horizontal ? $point->x : $point->y;
            }
        }

        return $values;
    }
}
