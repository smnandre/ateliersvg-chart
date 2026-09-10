<?php

declare(strict_types=1);

namespace Atelier\Chart\Theme;

use Atelier\Chart\Exception\InvalidArgumentException;

final readonly class Theme
{
    /**
     * @param non-empty-list<string> $palette
     */
    public function __construct(
        public string $backgroundColor,
        public string $textColor,
        public string $mutedTextColor,
        public string $gridColor,
        public string $axisColor,
        public array $palette,
        public string $fontFamily = 'Inter, ui-sans-serif, system-ui, sans-serif',
    ) {
        if ([] === $palette) {
            throw new InvalidArgumentException('A chart theme requires at least one palette color.');
        }
        foreach ([...$palette, $backgroundColor, $textColor, $mutedTextColor, $gridColor, $axisColor] as $color) {
            if ('' === trim($color)) {
                throw new InvalidArgumentException('Chart theme colors must not be empty.');
            }
        }
        if ('' === trim($fontFamily)) {
            throw new InvalidArgumentException('Chart theme font family must not be empty.');
        }
    }

    public static function default(): self
    {
        return new self(
            '#010205',
            '#e5e8ed',
            '#b8bec8',
            '#1b1f26',
            '#303338',
            ['#48c5ff', '#a58fff', '#f4a34b', '#06bfa8', '#ec79a9', '#5bae5f'],
            'system-ui, -apple-system, sans-serif',
        );
    }

    public static function dark(): self
    {
        return new self(
            '#151821',
            '#f5f7fb',
            '#aab2c3',
            '#2b3040',
            '#596174',
            ['#8b9cff', '#5ed6c0', '#ff9b7b', '#f2c85b', '#c49aff'],
        );
    }

    public static function alto(): self
    {
        return new self(
            '#050505',
            '#ffffff',
            '#888888',
            '#222222',
            '#444444',
            ['#8cc63f', '#b07030', '#5d3fd3', '#66cccc', '#e01b5d', '#005ce6'],
        );
    }

    public static function mono(): self
    {
        return new self(
            '#ffffff',
            '#161616',
            '#666666',
            '#e6e6e6',
            '#a0a0a0',
            ['#1f2937', '#6b7280', '#9ca3af', '#374151'],
        );
    }

    public static function warm(): self
    {
        return new self(
            '#fffaf4',
            '#352a24',
            '#786a61',
            '#eadfd4',
            '#b9a99c',
            ['#d46345', '#2f7d77', '#d19a2c', '#755f9f', '#b94b68'],
        );
    }

    public function colorAt(int $index): string
    {
        $count = count($this->palette);

        return $this->palette[(($index % $count) + $count) % $count];
    }

    /**
     * Derive a theme while preserving every visual role that is not overridden.
     *
     * @param non-empty-list<string>|null $palette
     */
    public function with(
        ?string $backgroundColor = null,
        ?string $textColor = null,
        ?string $mutedTextColor = null,
        ?string $gridColor = null,
        ?string $axisColor = null,
        ?array $palette = null,
        ?string $fontFamily = null,
    ): self {
        return new self(
            $backgroundColor ?? $this->backgroundColor,
            $textColor ?? $this->textColor,
            $mutedTextColor ?? $this->mutedTextColor,
            $gridColor ?? $this->gridColor,
            $axisColor ?? $this->axisColor,
            $palette ?? $this->palette,
            $fontFamily ?? $this->fontFamily,
        );
    }
}
