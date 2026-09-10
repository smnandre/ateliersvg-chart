<?php

declare(strict_types=1);

namespace Atelier\Chart\Builder;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Internal\Input;
use Atelier\Chart\Model\CartesianChart;
use Atelier\Chart\Model\ChartKind;
use Atelier\Chart\Model\Series;
use Atelier\Chart\Scale\Domain;

final class CartesianChartBuilder
{
    /** @var list<string> */
    private array $categories = [];

    /** @var list<Series> */
    private array $series = [];

    private string $title;
    private ?string $description = null;
    private float $width = 720.0;
    private float $height = 420.0;
    private bool $showValues;
    private bool $smooth = false;
    private Domain $domain;

    public function __construct(private readonly ChartKind $kind)
    {
        $this->title = $kind->defaultTitle();
        $this->showValues = ChartKind::Bar === $kind;
        $this->domain = Domain::automatic(ChartKind::Line !== $kind);
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

    public function smooth(bool $smooth = true): self
    {
        if (!\in_array($this->kind, [ChartKind::Line, ChartKind::Area], true)) {
            throw new InvalidArgumentException('Only line and area charts can use smooth curves.');
        }

        $this->smooth = $smooth;

        return $this;
    }

    public function includeZero(bool $include = true): self
    {
        $this->domain = Domain::automatic($include);

        return $this;
    }

    public function domain(float $minimum, float $maximum): self
    {
        $this->domain = Domain::fixed($minimum, $maximum);

        return $this;
    }

    /**
     * @param non-empty-array<int|string, int|float> $values
     */
    public function series(string $name, array $values, ?string $color = null): self
    {
        $categories = [];
        $numericValues = [];

        foreach ($values as $category => $value) {
            $categories[] = (string) $category;
            $numericValues[] = Input::number($value, 'Series value');
        }

        if ([] !== $this->categories && $categories !== $this->categories) {
            throw new InvalidArgumentException('Every series must use the same categories in the same order.');
        }

        $this->categories = $categories;
        $this->series[] = new Series($name, $numericValues, $color);

        return $this;
    }

    public function build(): CartesianChart
    {
        if ([] === $this->series || [] === $this->categories) {
            throw new InvalidArgumentException('Add at least one data series before building a chart.');
        }

        return new CartesianChart(
            $this->kind,
            $this->categories,
            $this->series,
            $this->title,
            $this->description,
            $this->width,
            $this->height,
            $this->showValues,
            $this->smooth,
            $this->domain,
        );
    }
}
