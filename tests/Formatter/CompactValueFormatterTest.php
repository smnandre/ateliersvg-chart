<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Formatter;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Formatter\CompactValueFormatter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CompactValueFormatter::class)]
final class CompactValueFormatterTest extends TestCase
{
    #[Test]
    public function formatsPlainAndCompactValues(): void
    {
        $formatter = new CompactValueFormatter(2);

        self::assertSame('1.23', $formatter->format(1.234));
        self::assertSame('1.25k', $formatter->format(1_250.0));
        self::assertSame('2M', $formatter->format(2_000_000.0));
    }

    #[Test]
    public function rejectsInvalidConfiguration(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CompactValueFormatter(15);
    }

    #[Test]
    public function rejectsNonFiniteValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new CompactValueFormatter())->format(INF);
    }
}
