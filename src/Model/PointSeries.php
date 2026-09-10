<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

use Atelier\Chart\Exception\InvalidArgumentException;

final readonly class PointSeries
{
    /**
     * @param non-empty-list<Point> $points
     */
    public function __construct(
        public string $name,
        public array $points,
        public ?string $color = null,
    ) {
        if ('' === trim($name)) {
            throw new InvalidArgumentException('Point series names must not be empty.');
        }
        if ([] === $points) {
            throw new InvalidArgumentException('Point series must contain at least one point.');
        }
        if (null !== $color && '' === trim($color)) {
            throw new InvalidArgumentException('Point series colors must not be empty when provided.');
        }
    }
}
