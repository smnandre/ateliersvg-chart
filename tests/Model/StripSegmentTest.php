<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\StripSegment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(StripSegment::class)]
final class StripSegmentTest extends TestCase
{
    #[Test]
    public function keepsWeightLabelAndOptionalColor(): void
    {
        $segment = new StripSegment(weight: 12, label: 'Production', color: '#06bfa8');
        self::assertSame(12.0, $segment->weight);
        self::assertSame('Production', $segment->label);
        self::assertSame('#06bfa8', $segment->color);
        self::assertNull((new StripSegment(1, 'Free text'))->color);
    }

    /** @return iterable<string, array{float, string, ?string}> */
    public static function invalidSegments(): iterable
    {
        yield 'zero' => [0, 'A', null];
        yield 'negative' => [-1, 'A', null];
        yield 'infinite' => [INF, 'A', null];
        yield 'not a number' => [NAN, 'A', null];
        yield 'empty label' => [1, ' ', null];
        yield 'empty color' => [1, 'A', ' '];
    }

    #[Test]
    #[DataProvider('invalidSegments')]
    public function rejectsInvalidSegments(float $weight, string $label, ?string $color): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StripSegment($weight, $label, $color);
    }
}
