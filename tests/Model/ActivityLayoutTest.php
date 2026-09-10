<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Model\ActivityLayout;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ActivityLayout::class)]
final class ActivityLayoutTest extends TestCase
{
    #[Test]
    public function exposesLayoutValues(): void
    {
        self::assertSame('calendar', ActivityLayout::Calendar->value);
        self::assertSame('strip', ActivityLayout::Strip->value);
    }
}
