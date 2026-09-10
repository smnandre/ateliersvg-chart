<?php

declare(strict_types=1);

namespace Atelier\Chart\Builder;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Internal\Input;
use Atelier\Chart\Model\ActivityCell;
use Atelier\Chart\Model\ActivityChart;
use Atelier\Chart\Model\ActivityLayout;

final class ActivityChartBuilder
{
    private ?string $seriesName = null;

    /** @var list<ActivityCell> */
    private array $cells = [];

    private ActivityLayout $layout = ActivityLayout::Calendar;
    private string $title = 'Activity';
    private ?string $description = null;
    private ?string $summary = null;
    private float $width = 720.0;
    private float $height = 320.0;

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

    public function summary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function size(float $width, float $height): self
    {
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    public function calendar(): self
    {
        $this->layout = ActivityLayout::Calendar;

        return $this;
    }

    /**
     * Lays every cell on a single row instead of wrapping into a seven row matrix.
     */
    public function strip(): self
    {
        $this->layout = ActivityLayout::Strip;

        return $this;
    }

    /**
     * @deprecated since 0.1, use {@see self::strip()} instead. The cells carry no time axis,
     *             so "timeline" promised an ordering by duration the model never had.
     */
    public function timeline(): self
    {
        return $this->strip();
    }

    /**
     * @param non-empty-array<int|string, int|float|array{value: int|float, color?: string, label?: string}> $values
     */
    public function series(string $name, array $values, ?string $color = null): self
    {
        if (null !== $this->seriesName) {
            throw new InvalidArgumentException('Activity charts contain exactly one series.');
        }

        $this->seriesName = $name;

        foreach ($values as $category => $datum) {
            if (is_array($datum)) {
                $this->cells[] = $this->createCell((string) $category, $datum, $color);

                continue;
            }

            $this->cells[] = new ActivityCell((string) $category, Input::number($datum, 'Activity value'), $color);
        }

        return $this;
    }

    public function build(): ActivityChart
    {
        if (null === $this->seriesName || [] === $this->cells) {
            throw new InvalidArgumentException('Add one non-empty data series before building an activity chart.');
        }

        return new ActivityChart(
            $this->seriesName,
            $this->cells,
            $this->layout,
            $this->title,
            $this->description,
            $this->summary,
            $this->width,
            $this->height,
        );
    }

    /**
     * @param array{value?: mixed, color?: mixed, label?: mixed} $datum
     */
    private function createCell(string $category, array $datum, ?string $seriesColor): ActivityCell
    {
        if (!array_key_exists('value', $datum) || (!is_int($datum['value']) && !is_float($datum['value']))) {
            throw new InvalidArgumentException('Styled activity cells require a numeric value.');
        }

        $color = $datum['color'] ?? $seriesColor;
        $label = $datum['label'] ?? null;

        if (null !== $color && !is_string($color)) {
            throw new InvalidArgumentException('Activity cell color must be a string when provided.');
        }
        if (null !== $label && !is_string($label)) {
            throw new InvalidArgumentException('Activity cell label must be a string when provided.');
        }

        return new ActivityCell($category, (float) $datum['value'], $color, $label);
    }
}
