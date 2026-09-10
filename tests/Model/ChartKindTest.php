<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Model\ChartKind;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChartKind::class)]
final class ChartKindTest extends TestCase
{
    #[Test]
    public function providesAccessibleDefaultTitles(): void
    {
        self::assertSame('Bar chart', ChartKind::Bar->defaultTitle());
        self::assertSame('Line chart', ChartKind::Line->defaultTitle());
        self::assertSame('Area chart', ChartKind::Area->defaultTitle());
        self::assertSame('Radar chart', ChartKind::Radar->defaultTitle());
    }
}
