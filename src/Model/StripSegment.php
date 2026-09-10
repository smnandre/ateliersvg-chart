<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

use Atelier\Chart\Exception\InvalidArgumentException;

final readonly class StripSegment
{
    public function __construct(
        public float $weight,
        public string $label,
        public ?string $color = null,
    ) {
        if (!is_finite($weight) || $weight <= 0.0) {
            throw new InvalidArgumentException('Strip segment weights must be positive finite numbers.');
        }
        if ('' === trim($label)) {
            throw new InvalidArgumentException('Strip segment labels must not be empty.');
        }
        if (null !== $color && '' === trim($color)) {
            throw new InvalidArgumentException('Strip segment colors must not be empty when provided.');
        }
    }
}
