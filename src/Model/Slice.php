<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

use Atelier\Chart\Exception\InvalidArgumentException;

final readonly class Slice
{
    public function __construct(
        public string $label,
        public float $value,
        public ?string $color = null,
    ) {
        if ('' === trim($label)) {
            throw new InvalidArgumentException('Slice labels must not be empty.');
        }
        if (!is_finite($value) || $value <= 0.0) {
            throw new InvalidArgumentException('Slice values must be positive finite numbers.');
        }
        if (null !== $color && '' === trim($color)) {
            throw new InvalidArgumentException('Slice colors must not be empty when provided.');
        }
    }
}
