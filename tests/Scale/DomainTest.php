<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Scale;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Scale\Domain;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Domain::class)]
final class DomainTest extends TestCase
{
    #[Test]
    public function supportsAutomaticAndFixedDomains(): void
    {
        self::assertTrue(Domain::automatic(true)->includeZero);
        self::assertFalse(Domain::automatic()->isFixed());
        self::assertTrue(Domain::fixed(10.0, 20.0)->isFixed());
    }

    #[Test]
    public function rejectsInvalidFixedBounds(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Domain::fixed(20.0, 10.0);
    }

    #[Test]
    public function rejectsAnOverflowingSpan(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Domain::fixed(-1.0e308, 1.0e308);
    }
}
