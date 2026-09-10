<?php

declare(strict_types=1);

namespace Atelier\Chart\Builder;

use Atelier\Chart\Internal\Input;
use Atelier\Chart\Model\Sparkline;

final class SparklineBuilder
{
    /** @var non-empty-list<float> */
    private readonly array $values;

    private string $title = 'Sparkline chart';
    private ?string $description = null;
    private float $width = 240.0;
    private float $height = 64.0;
    private ?string $color = null;

    /**
     * @param non-empty-list<int|float> $values
     */
    public function __construct(array $values)
    {
        $this->values = array_map(static fn (mixed $value): float => Input::number($value, 'Sparkline value'), $values);
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

    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function build(): Sparkline
    {
        return new Sparkline(
            $this->values,
            $this->title,
            $this->description,
            $this->width,
            $this->height,
            $this->color,
        );
    }
}
