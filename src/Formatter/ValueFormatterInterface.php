<?php

declare(strict_types=1);

namespace Atelier\Chart\Formatter;

interface ValueFormatterInterface
{
    public function format(float $value): string;
}
