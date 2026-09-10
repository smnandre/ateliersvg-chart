<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Internal;

use Atelier\Chart\Internal\Number;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Number::class)]
final class NumberTest extends TestCase
{
    public function testRoundTripPreservesFloatsAndRestoresHostPrecision(): void
    {
        $previous = ini_set('serialize_precision', '3');
        try {
            foreach ([1.23456789, 0.45, 1e-100, 1e100, -1.23456789] as $value) {
                self::assertSame($value, (float) Number::serialize($value));
                self::assertSame('3', ini_get('serialize_precision'));
            }
        } finally {
            ini_set('serialize_precision', $previous);
        }
    }

    public function testPrecisionIsRestoredWhenJsonEncodingFails(): void
    {
        $previous = ini_set('serialize_precision', '3');
        try {
            $this->expectException(\JsonException::class);
            try {
                Number::serialize(INF);
            } finally {
                self::assertSame('3', ini_get('serialize_precision'));
            }
        } finally {
            ini_set('serialize_precision', $previous);
        }
    }
}
