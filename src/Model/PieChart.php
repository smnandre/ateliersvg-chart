<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Internal\Input;

final readonly class PieChart implements ChartModel
{
    /**
     * @param non-empty-list<Slice> $slices
     */
    public function __construct(
        public array $slices,
        public float $innerRadiusRatio,
        private string $chartTitle,
        private ?string $chartDescription = null,
        private float $chartWidth = 720.0,
        private float $chartHeight = 420.0,
    ) {
        if ([] === $slices) {
            throw new InvalidArgumentException('Pie charts require at least one slice.');
        }
        if ($innerRadiusRatio < 0.0 || $innerRadiusRatio > 0.75) {
            throw new InvalidArgumentException('The inner radius ratio must be between zero and 0.75.');
        }

        $labels = [];
        foreach ($slices as $slice) {
            $labels[] = $slice->label;
        }
        if (count($labels) !== count(array_unique($labels))) {
            throw new InvalidArgumentException('Pie chart slice labels must be unique.');
        }
        if ('' === trim($chartTitle)) {
            throw new InvalidArgumentException('Pie chart title must not be empty.');
        }
        if (null !== $chartDescription && '' === trim($chartDescription)) {
            throw new InvalidArgumentException('Pie chart description must not be empty when provided.');
        }
        Input::dimensions($chartWidth, $chartHeight, 280.0, 240.0, 'Pie charts');
        Input::finiteSum(array_map(static fn (Slice $slice): float => $slice->value, $slices), 'Pie slices');
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

    public function total(): float
    {
        return Input::finiteSum(array_map(static fn (Slice $slice): float => $slice->value, $this->slices), 'Pie slices');
    }

    public function isDonut(): bool
    {
        return $this->innerRadiusRatio > 0.0;
    }
}
