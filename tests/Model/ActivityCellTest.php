<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\ActivityCell;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ActivityCell::class)]
final class ActivityCellTest extends TestCase
{
    #[Test]
    public function exposesGenericCellData(): void
    {
        $cell = new ActivityCell('Today', 3.0, '#123456', 'Busy');

        self::assertSame('Today', $cell->category);
        self::assertSame(3.0, $cell->value);
        self::assertSame('#123456', $cell->color);
        self::assertSame('Busy', $cell->label);
    }

    #[Test]
    public function rejectsAnEmptyCategory(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ActivityCell(' ', 1.0);
    }

    #[Test]
    public function rejectsAnInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ActivityCell('Today', -1.0);
    }

    #[Test]
    public function rejectsAnEmptyColor(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ActivityCell('Today', 1.0, ' ');
    }

    #[Test]
    public function rejectsAnEmptyLabel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ActivityCell('Today', 1.0, label: ' ');
    }
}
