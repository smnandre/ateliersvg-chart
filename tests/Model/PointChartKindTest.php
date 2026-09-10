<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Model\PointChartKind;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PointChartKind::class)]
final class PointChartKindTest extends TestCase
{
    #[Test]
    public function providesFamilyTitles(): void
    {
        self::assertSame('Scatter plot', PointChartKind::Scatter->defaultTitle());
        self::assertSame('Bubble chart', PointChartKind::Bubble->defaultTitle());
    }
}
