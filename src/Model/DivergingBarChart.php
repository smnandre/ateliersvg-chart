<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Internal\Input;

final readonly class DivergingBarChart implements ChartModel
{
    /**
     * @param non-empty-list<string>      $categories
     * @param array{0: Series, 1: Series} $series
     */
    public function __construct(
        public BarOrientation $orientation,
        public array $categories,
        public array $series,
        private string $chartTitle,
        private ?string $chartDescription = null,
        private float $chartWidth = 720.0,
        private float $chartHeight = 420.0,
        public bool $showValues = false,
    ) {
        if ([] === $categories) {
            throw new InvalidArgumentException('Diverging bar categories must not be empty.');
        }
        if (2 !== count($series)) {
            throw new InvalidArgumentException('Diverging bar charts require exactly two series.');
        }
        if (count($categories) !== count(array_unique($categories))) {
            throw new InvalidArgumentException('Diverging bar categories must be unique.');
        }
        foreach ($categories as $category) {
            if ('' === trim($category)) {
                throw new InvalidArgumentException('Diverging bar categories must not contain empty labels.');
            }
        }
        foreach ($series as $dataSeries) {
            if (count($categories) !== count($dataSeries->values)) {
                throw new InvalidArgumentException('Every diverging series must contain one value per category.');
            }
            if (min($dataSeries->values) < 0.0) {
                throw new InvalidArgumentException('Diverging series values must be non-negative magnitudes.');
            }
        }
        if (max([...$series[0]->values, ...$series[1]->values]) <= 0.0) {
            throw new InvalidArgumentException('A diverging bar chart requires at least one positive magnitude.');
        }
        if ('' === trim($chartTitle)) {
            throw new InvalidArgumentException('Diverging bar title must not be empty.');
        }
        if (null !== $chartDescription && '' === trim($chartDescription)) {
            throw new InvalidArgumentException('Diverging bar description must not be empty when provided.');
        }
        Input::dimensions($chartWidth, $chartHeight, 320.0, 240.0, 'Diverging bar charts');
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

    public function maximumMagnitude(): float
    {
        return max([...$this->series[0]->values, ...$this->series[1]->values]);
    }
}
