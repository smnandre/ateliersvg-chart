<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Internal\Input;
use Atelier\Chart\Scale\Domain;

final readonly class CartesianChart implements ChartModel
{
    public Domain $domain;

    /**
     * @param non-empty-list<string> $categories
     * @param non-empty-list<Series> $series
     */
    public function __construct(
        public ChartKind $kind,
        public array $categories,
        public array $series,
        private string $chartTitle,
        private ?string $chartDescription = null,
        private float $chartWidth = 720.0,
        private float $chartHeight = 420.0,
        public bool $showValues = false,
        public bool $smooth = false,
        ?Domain $domain = null,
    ) {
        $this->domain = $domain ?? Domain::automatic(ChartKind::Line !== $kind);
        if ([] === $categories) {
            throw new InvalidArgumentException('Chart categories must not be empty.');
        }
        if ([] === $series) {
            throw new InvalidArgumentException('Chart series must not be empty.');
        }
        if (count($categories) !== count(array_unique($categories))) {
            throw new InvalidArgumentException('Chart categories must be unique.');
        }
        foreach ($categories as $category) {
            if ('' === trim($category)) {
                throw new InvalidArgumentException('Chart categories must not contain empty labels.');
            }
        }
        foreach ($series as $dataSeries) {
            if (count($categories) !== count($dataSeries->values)) {
                throw new InvalidArgumentException('Every series must contain one value per category.');
            }
            if (ChartKind::Radar === $kind && min($dataSeries->values) < 0.0) {
                throw new InvalidArgumentException('Radar chart values must not be negative.');
            }
        }
        if (ChartKind::Radar === $kind && count($categories) < 3) {
            throw new InvalidArgumentException('A radar chart requires at least three categories.');
        }
        if ('' === trim($chartTitle)) {
            throw new InvalidArgumentException('Chart title must not be empty.');
        }
        if (null !== $chartDescription && '' === trim($chartDescription)) {
            throw new InvalidArgumentException('Chart description must not be empty when provided.');
        }
        Input::dimensions($chartWidth, $chartHeight, 240.0, 180.0, 'Cartesian charts');
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
    public function values(): array
    {
        $values = [];

        foreach ($this->series as $dataSeries) {
            foreach ($dataSeries->values as $value) {
                $values[] = $value;
            }
        }

        return $values;
    }
}
