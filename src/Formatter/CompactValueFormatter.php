<?php

declare(strict_types=1);

namespace Atelier\Chart\Formatter;

use Atelier\Chart\Exception\InvalidArgumentException;

final readonly class CompactValueFormatter implements ValueFormatterInterface
{
    public function __construct(private int $fractionDigits = 1)
    {
        if ($fractionDigits < 0 || $fractionDigits > 14) {
            throw new InvalidArgumentException('Formatter fraction digits must be between zero and fourteen.');
        }
    }

    public function format(float $value): string
    {
        if (!is_finite($value)) {
            throw new InvalidArgumentException('Only finite chart values can be formatted.');
        }

        $absolute = abs($value);

        if ($absolute >= 1_000_000.0) {
            return $this->number($value / 1_000_000.0).'M';
        }
        if ($absolute >= 1_000.0) {
            return $this->number($value / 1_000.0).'k';
        }

        return $this->number($value);
    }

    private function number(float $value): string
    {
        $formatted = number_format($value, $this->fractionDigits, '.', '');

        return str_contains($formatted, '.') ? rtrim(rtrim($formatted, '0'), '.') : $formatted;
    }
}
