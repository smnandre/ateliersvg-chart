<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

use Atelier\Chart\Exception\InvalidArgumentException;

final readonly class ActivityCell
{
    public function __construct(
        public string $category,
        public float $value,
        public ?string $color = null,
        public ?string $label = null,
    ) {
        if ('' === trim($category)) {
            throw new InvalidArgumentException('Activity cell category must not be empty.');
        }
        if (!is_finite($value) || $value < 0.0) {
            throw new InvalidArgumentException('Activity cell value must be a finite non-negative number.');
        }
        if (null !== $color && '' === trim($color)) {
            throw new InvalidArgumentException('Activity cell color must not be empty when provided.');
        }
        if (null !== $label && '' === trim($label)) {
            throw new InvalidArgumentException('Activity cell label must not be empty when provided.');
        }
    }
}
