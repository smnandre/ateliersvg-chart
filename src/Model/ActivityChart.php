<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Internal\Input;

final readonly class ActivityChart implements ChartModel
{
    /**
     * @param non-empty-list<ActivityCell> $cells
     */
    public function __construct(
        public string $seriesName,
        public array $cells,
        public ActivityLayout $layout,
        private string $chartTitle = 'Activity',
        private ?string $chartDescription = null,
        public ?string $summary = null,
        private float $chartWidth = 720.0,
        private float $chartHeight = 320.0,
    ) {
        if ('' === trim($seriesName)) {
            throw new InvalidArgumentException('Activity series name must not be empty.');
        }
        if ([] === $cells) {
            throw new InvalidArgumentException('Activity cells must not be empty.');
        }

        $categories = array_map(static fn (ActivityCell $cell): string => $cell->category, $cells);

        if (count($categories) !== count(array_unique($categories))) {
            throw new InvalidArgumentException('Activity cell categories must be unique.');
        }
        if ('' === trim($chartTitle)) {
            throw new InvalidArgumentException('Activity chart title must not be empty.');
        }
        if (null !== $chartDescription && '' === trim($chartDescription)) {
            throw new InvalidArgumentException('Activity chart description must not be empty when provided.');
        }
        if (null !== $summary && '' === trim($summary)) {
            throw new InvalidArgumentException('Activity chart summary must not be empty when provided.');
        }
        Input::dimensions($chartWidth, $chartHeight, 240.0, 180.0, 'Activity charts');
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

    public function maximum(): float
    {
        return max(array_map(static fn (ActivityCell $cell): float => $cell->value, $this->cells));
    }
}
