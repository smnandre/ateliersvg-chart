<?php

declare(strict_types=1);

namespace Atelier\Chart\Builder;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\PieChart;
use Atelier\Chart\Model\Slice;

final class PieChartBuilder
{
    /** @var list<Slice> */
    private array $slices = [];

    private string $title;
    private ?string $description = null;
    private float $width = 720.0;
    private float $height = 420.0;

    public function __construct(private readonly float $innerRadiusRatio)
    {
        $this->title = $innerRadiusRatio > 0.0 ? 'Donut chart' : 'Pie chart';
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

    public function slice(string $label, float $value, ?string $color = null): self
    {
        $this->slices[] = new Slice($label, $value, $color);

        return $this;
    }

    public function build(): PieChart
    {
        if ([] === $this->slices) {
            throw new InvalidArgumentException('Add at least one slice before building a pie chart.');
        }

        return new PieChart(
            $this->slices,
            $this->innerRadiusRatio,
            $this->title,
            $this->description,
            $this->width,
            $this->height,
        );
    }
}
