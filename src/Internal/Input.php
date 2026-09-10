<?php

declare(strict_types=1);

namespace Atelier\Chart\Internal;

use Atelier\Chart\Exception\InvalidArgumentException;

final class Input
{
    private function __construct()
    {
    }

    public static function number(mixed $value, string $context): float
    {
        if (!is_int($value) && !is_float($value)) {
            throw new InvalidArgumentException($context.' must be a number.');
        }

        $number = (float) $value;

        if (!is_finite($number)) {
            throw new InvalidArgumentException($context.' must be finite.');
        }

        return $number;
    }

    public static function dimensions(float $width, float $height, float $minimumWidth, float $minimumHeight, string $chart): void
    {
        if (!is_finite($width) || !is_finite($height) || $width < $minimumWidth || $height < $minimumHeight) {
            throw new InvalidArgumentException(sprintf('%s must be at least %g by %g finite pixels.', $chart, $minimumWidth, $minimumHeight));
        }
    }

    /**
     * @param iterable<float> $values
     */
    public static function finiteSum(iterable $values, string $context): float
    {
        $sum = 0.0;

        foreach ($values as $value) {
            $sum += $value;

            if (!is_finite($sum)) {
                throw new InvalidArgumentException($context.' must have a finite total.');
            }
        }

        return $sum;
    }
}
