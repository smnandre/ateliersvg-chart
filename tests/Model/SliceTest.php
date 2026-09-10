<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\Slice;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Slice::class)]
final class SliceTest extends TestCase
{
    #[Test]
    public function exposesAValidSlice(): void
    {
        $slice = new Slice('Direct', 45.0, '#123456');

        self::assertSame('Direct', $slice->label);
        self::assertSame(45.0, $slice->value);
        self::assertSame('#123456', $slice->color);
    }

    #[Test]
    public function rejectsNonPositiveValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Slice('Direct', 0.0);
    }

    #[Test]
    public function rejectsAnEmptyLabel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Slice(' ', 45.0);
    }

    #[Test]
    public function rejectsAnEmptyColor(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Slice('Direct', 45.0, ' ');
    }
}
