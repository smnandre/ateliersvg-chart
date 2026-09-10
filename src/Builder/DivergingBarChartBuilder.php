<?php

declare(strict_types=1);

namespace Atelier\Chart\Builder;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Internal\Input;
use Atelier\Chart\Model\BarOrientation;
use Atelier\Chart\Model\DivergingBarChart;
use Atelier\Chart\Model\Series;

final class DivergingBarChartBuilder
{
    /** @var list<string> */
    private array $categories = [];

    /** @var list<Series> */
    private array $series = [];

    private BarOrientation $orientation = BarOrientation::Vertical;
    private string $title = 'Diverging bar chart';
    private ?string $description = null;
    private float $width = 720.0;
    private float $height = 420.0;
    private bool $showValues = false;

    public function horizontal(): self
    {
        $this->orientation = BarOrientation::Horizontal;

        return $this;
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

    public function showValues(bool $show = true): self
    {
        $this->showValues = $show;

        return $this;
    }

    /**
     * @param non-empty-array<int|string, int|float> $values
     */
    public function series(string $name, array $values, ?string $color = null): self
    {
        if (2 <= count($this->series)) {
            throw new InvalidArgumentException('Diverging bar charts accept exactly two series.');
        }

        $categories = [];
        $numericValues = [];

        foreach ($values as $category => $value) {
            $categories[] = (string) $category;
            $numericValues[] = Input::number($value, 'Diverging bar value');
        }

        if ([] !== $this->categories && $categories !== $this->categories) {
            throw new InvalidArgumentException('Every diverging series must use the same categories in the same order.');
        }

        $this->categories = $categories;
        $this->series[] = new Series($name, $numericValues, $color);

        return $this;
    }

    public function build(): DivergingBarChart
    {
        if (2 !== count($this->series)) {
            throw new InvalidArgumentException('Add exactly two series before building a diverging bar chart.');
        }
        if ([] === $this->categories) {
            throw new InvalidArgumentException('Add non-empty series before building a diverging bar chart.');
        }

        return new DivergingBarChart(
            $this->orientation,
            $this->categories,
            [$this->series[0], $this->series[1]],
            $this->title,
            $this->description,
            $this->width,
            $this->height,
            $this->showValues,
        );
    }
}
