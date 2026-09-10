<?php

declare(strict_types=1);

namespace Atelier\Chart\Scale;

use Atelier\Chart\Exception\InvalidArgumentException;

final readonly class Domain
{
    private function __construct(
        public bool $includeZero,
        public ?float $minimum = null,
        public ?float $maximum = null,
    ) {
        if ((null === $minimum) !== (null === $maximum)) {
            throw new InvalidArgumentException('A fixed domain requires both a minimum and a maximum.');
        }
        if (null !== $minimum && null !== $maximum && (!is_finite($minimum) || !is_finite($maximum) || !is_finite($maximum - $minimum) || $minimum >= $maximum)) {
            throw new InvalidArgumentException('A fixed domain requires finite bounds with a minimum below its maximum.');
        }
    }

    public static function automatic(bool $includeZero = false): self
    {
        return new self($includeZero);
    }

    public static function fixed(float $minimum, float $maximum): self
    {
        return new self(false, $minimum, $maximum);
    }

    public function isFixed(): bool
    {
        return null !== $this->minimum;
    }
}
