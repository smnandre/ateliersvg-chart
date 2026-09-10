<?php

declare(strict_types=1);

namespace Atelier\Chart\Model;

use Atelier\Chart\Exception\InvalidArgumentException;

final readonly class Series
{
    /**
     * @param non-empty-list<float> $values
     */
    public function __construct(
        public string $name,
        public array $values,
        public ?string $color = null,
    ) {
        if ('' === trim($name)) {
            throw new InvalidArgumentException('Series name must not be empty.');
        }
        if ([] === $values) {
            throw new InvalidArgumentException('Series values must not be empty.');
        }
        if (null !== $color && '' === trim($color)) {
            throw new InvalidArgumentException('Series color must not be empty when provided.');
        }
        foreach ($values as $value) {
            if (!is_finite($value)) {
                throw new InvalidArgumentException('Series values must be finite numbers.');
            }
        }
    }
}
