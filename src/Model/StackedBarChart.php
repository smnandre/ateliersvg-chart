<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Internal\Input;

final readonly class StackedBarChart implements ChartModel
{
    /**
     * @param non-empty-list<string> $categories
     * @param non-empty-list<Series> $series
     */
    public function __construct(
        public array $categories,
        public array $series,
        public BarOrientation $orientation,
        private string $chartTitle = 'Stacked bar chart',
        private ?string $chartDescription = null,
        private float $chartWidth = 720.0,
        private float $chartHeight = 420.0,
        public bool $showValues = false,
    ) {
        if ([] === $categories) {
            throw new InvalidArgumentException('Stacked bar categories must not be empty.');
        }
        if ([] === $series) {
            throw new InvalidArgumentException('Stacked bar series must not be empty.');
        }
        if (count($categories) !== count(array_unique($categories))) {
            throw new InvalidArgumentException('Stacked bar categories must be unique.');
        }
        foreach ($categories as $category) {
            if ('' === trim($category)) {
                throw new InvalidArgumentException('Stacked bar categories must not contain empty labels.');
            }
        }
        foreach ($series as $dataSeries) {
            if (count($categories) !== count($dataSeries->values)) {
                throw new InvalidArgumentException('Every stacked series must contain one value per category.');
            }
            if (min($dataSeries->values) < 0.0) {
                throw new InvalidArgumentException('Stacked bar values must not be negative.');
            }
        }
        if ('' === trim($chartTitle)) {
            throw new InvalidArgumentException('Stacked bar title must not be empty.');
        }
        if (null !== $chartDescription && '' === trim($chartDescription)) {
            throw new InvalidArgumentException('Stacked bar description must not be empty when provided.');
        }
        Input::dimensions($chartWidth, $chartHeight, 240.0, 180.0, 'Stacked bar charts');
        $this->finiteTotals($categories, $series);
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

    /**
     * @return non-empty-list<float>
     */
    public function totals(): array
    {
        $totals = array_fill(0, count($this->categories), 0.0);

        foreach ($this->series as $dataSeries) {
            foreach ($dataSeries->values as $index => $value) {
                $totals[$index] += $value;
            }
        }

        return array_values($totals);
    }

    /**
     * @param non-empty-list<string> $categories
     * @param non-empty-list<Series> $series
     */
    private function finiteTotals(array $categories, array $series): void
    {
        foreach (array_keys($categories) as $index) {
            Input::finiteSum(array_map(static fn (Series $item): float => $item->values[$index], $series), 'Stacked bar series');
        }
    }
}
