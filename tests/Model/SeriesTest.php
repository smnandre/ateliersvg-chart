<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\Series;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Series::class)]
final class SeriesTest extends TestCase
{
    #[Test]
    public function preservesTypedValues(): void
    {
        $series = new Series('Revenue', [12.0, 18.0], '#5a67d8');

        self::assertSame('Revenue', $series->name);
        self::assertSame([12.0, 18.0], $series->values);
        self::assertSame('#5a67d8', $series->color);
    }

    #[Test]
    public function rejectsAnEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Series(' ', [12.0]);
    }

    #[Test]
    public function rejectsEmptyValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Series('Revenue', []);
    }

    #[Test]
    public function rejectsNonFiniteValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Series('Revenue', [INF]);
    }

    #[Test]
    public function rejectsAnEmptyCustomColor(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Series('Revenue', [12.0], ' ');
    }
}
