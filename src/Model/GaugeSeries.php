<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

use Atelier\Chart\Exception\InvalidArgumentException;

final readonly class GaugeSeries
{
    public function __construct(
        public string $name,
        public float $value,
        public ?string $color = null,
    ) {
        if ('' === trim($name)) {
            throw new InvalidArgumentException('Gauge series names must not be empty.');
        }
        if (!is_finite($value)) {
            throw new InvalidArgumentException('Gauge series values must be finite numbers.');
        }
        if (null !== $color && '' === trim($color)) {
            throw new InvalidArgumentException('Gauge series colors must not be empty when provided.');
        }
    }
}
