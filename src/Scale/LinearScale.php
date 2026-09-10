<?php

declare(strict_types=1);

namespace Atelier\Chart\Scale;

use Atelier\Chart\Exception\InvalidArgumentException;

final readonly class LinearScale
{
    private function __construct(
        public float $domainMin,
        public float $domainMax,
        public float $rangeStart,
        public float $rangeEnd,
        public float $tickStep,
    ) {
    }

    /**
     * @param non-empty-list<float> $values
     */
    public static function forValues(
        array $values,
        float $rangeStart,
        float $rangeEnd,
        int $tickCount = 5,
        bool $includeZero = true,
        ?Domain $domain = null,
    ): self {
        if ($tickCount < 2) {
            throw new InvalidArgumentException('A linear scale requires at least two ticks.');
        }

        if (!is_finite($rangeStart) || !is_finite($rangeEnd) || !is_finite($rangeEnd - $rangeStart) || $rangeStart === $rangeEnd) {
            throw new InvalidArgumentException('A linear scale requires distinct finite range bounds.');
        }
        if ([] === $values) {
            throw new InvalidArgumentException('A linear scale requires at least one value.');
        }
        foreach ($values as $value) {
            if (!is_finite($value)) {
                throw new InvalidArgumentException('A linear scale requires finite values.');
            }
        }

        $domain ??= Domain::automatic($includeZero);

        if ($domain->isFixed()) {
            $minimum = $domain->minimum;
            $maximum = $domain->maximum;
            \assert(null !== $minimum && null !== $maximum);
            if (min($values) < $minimum || max($values) > $maximum) {
                throw new InvalidArgumentException('Chart values must fit inside the fixed scale domain.');
            }
            $step = self::niceStep(($maximum - $minimum) / ($tickCount - 1));

            return new self($minimum, $maximum, $rangeStart, $rangeEnd, $step);
        }

        $minimum = min($values);
        $maximum = max($values);

        if ($domain->includeZero) {
            $minimum = min(0.0, $minimum);
            $maximum = max(0.0, $maximum);
        }

        if ($minimum === $maximum) {
            $padding = 0.0 === $minimum ? 1.0 : abs($minimum) * 0.1;
            $minimum -= $padding;
            $maximum += $padding;
        }

        $step = self::niceStep(($maximum - $minimum) / ($tickCount - 1));
        $niceMinimum = floor($minimum / $step) * $step;
        $niceMaximum = ceil($maximum / $step) * $step;

        if ($niceMinimum === $niceMaximum) {
            $niceMaximum += $step;
        }

        if (!is_finite($niceMinimum) || !is_finite($niceMaximum) || !is_finite($niceMaximum - $niceMinimum)) {
            throw new InvalidArgumentException('The scale domain is too large to represent.');
        }

        return new self($niceMinimum, $niceMaximum, $rangeStart, $rangeEnd, $step);
    }

    public function map(float $value): float
    {
        if (!is_finite($value)) {
            throw new InvalidArgumentException('Only finite values can be mapped by a linear scale.');
        }
        $ratio = ($value - $this->domainMin) / ($this->domainMax - $this->domainMin);

        return $this->rangeStart + $ratio * ($this->rangeEnd - $this->rangeStart);
    }

    /**
     * @return non-empty-list<float>
     */
    public function ticks(): array
    {
        $ticks = [$this->domainMin];
        $first = ceil($this->domainMin / $this->tickStep) * $this->tickStep;
        $count = (int) ceil(($this->domainMax - $this->domainMin) / $this->tickStep);

        for ($index = 0; $index <= $count; ++$index) {
            $value = $this->roundTick($first + $index * $this->tickStep);
            if ($value >= $this->domainMax) {
                break;
            }
            if ($value > $ticks[array_key_last($ticks)]) {
                $ticks[] = $value;
            }
        }

        $ticks[] = $this->domainMax;

        return $ticks;
    }

    private static function niceStep(float $roughStep): float
    {
        if (!is_finite($roughStep) || $roughStep <= 0.0) {
            throw new InvalidArgumentException('The scale domain is too large or too small to represent.');
        }
        $magnitude = 10.0 ** floor(log10($roughStep));
        if (0.0 === $magnitude) {
            throw new InvalidArgumentException('The scale tick step is too small to represent.');
        }
        $normalized = $roughStep / $magnitude;
        $factor = match (true) {
            $normalized <= 1.0 => 1.0,
            $normalized <= 2.0 => 2.0,
            $normalized <= 5.0 => 5.0,
            default => 10.0,
        };

        $step = $factor * $magnitude;
        if (!is_finite($step)) {
            throw new InvalidArgumentException('The scale tick step is too large to represent.');
        }

        return $step;
    }

    private function roundTick(float $value): float
    {
        return round($value, max(0, (int) -floor(log10($this->tickStep))));
    }
}
