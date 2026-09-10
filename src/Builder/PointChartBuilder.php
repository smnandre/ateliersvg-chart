<?php

declare(strict_types=1);

namespace Atelier\Chart\Builder;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Internal\Input;
use Atelier\Chart\Model\Point;
use Atelier\Chart\Model\PointChart;
use Atelier\Chart\Model\PointChartKind;
use Atelier\Chart\Model\PointSeries;
use Atelier\Chart\Scale\Domain;

final class PointChartBuilder
{
    /** @var list<PointSeries> */
    private array $series = [];

    private string $title;
    private ?string $description = null;
    private float $width = 720.0;
    private float $height = 420.0;
    private ?string $xLabel = null;
    private ?string $yLabel = null;
    private Domain $xDomain;
    private Domain $yDomain;

    public function __construct(private readonly PointChartKind $kind)
    {
        $this->title = $kind->defaultTitle();
        $this->xDomain = Domain::automatic();
        $this->yDomain = Domain::automatic();
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

    public function axes(string $xLabel, string $yLabel): self
    {
        $this->xLabel = $xLabel;
        $this->yLabel = $yLabel;

        return $this;
    }

    public function includeZero(bool $include = true): self
    {
        $this->xDomain = Domain::automatic($include);
        $this->yDomain = Domain::automatic($include);

        return $this;
    }

    public function xDomain(float $minimum, float $maximum): self
    {
        $this->xDomain = Domain::fixed($minimum, $maximum);

        return $this;
    }

    public function yDomain(float $minimum, float $maximum): self
    {
        $this->yDomain = Domain::fixed($minimum, $maximum);

        return $this;
    }

    /**
     * @param non-empty-list<array{x: int|float, y: int|float, size?: int|float}> $points
     */
    public function series(string $name, array $points, ?string $color = null): self
    {
        $pointModels = [];

        foreach ($points as $point) {
            if (!is_array($point) || !array_key_exists('x', $point) || !array_key_exists('y', $point)) {
                throw new InvalidArgumentException('Every point requires numeric x and y values.');
            }
            if (PointChartKind::Bubble === $this->kind && !array_key_exists('size', $point)) {
                throw new InvalidArgumentException('Bubble points require a size value.');
            }

            $pointModels[] = new Point(
                Input::number($point['x'], 'Point x'),
                Input::number($point['y'], 'Point y'),
                array_key_exists('size', $point) ? Input::number($point['size'], 'Point size') : 1.0,
            );
        }

        $this->series[] = new PointSeries($name, $pointModels, $color);

        return $this;
    }

    public function build(): PointChart
    {
        if ([] === $this->series) {
            throw new InvalidArgumentException('Add at least one series before building a point chart.');
        }

        return new PointChart(
            $this->kind,
            $this->series,
            $this->title,
            $this->description,
            $this->width,
            $this->height,
            $this->xLabel,
            $this->yLabel,
            $this->xDomain,
            $this->yDomain,
        );
    }
}
