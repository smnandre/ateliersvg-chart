<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

use Atelier\Chart\Exception\InvalidArgumentException;

final readonly class Point
{
    public function __construct(
        public float $x,
        public float $y,
        public float $size = 1.0,
    ) {
        if (!is_finite($x) || !is_finite($y) || !is_finite($size)) {
            throw new InvalidArgumentException('Point coordinates and sizes must be finite numbers.');
        }
        if ($size <= 0.0) {
            throw new InvalidArgumentException('Point sizes must be positive.');
        }
    }
}
