<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Theme;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Theme\Theme;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Theme::class)]
final class ThemeTest extends TestCase
{
    #[Test]
    public function providesFiveCoherentPresets(): void
    {
        self::assertSame('#010205', Theme::default()->backgroundColor);
        self::assertSame('#48c5ff', Theme::default()->colorAt(0));
        self::assertSame('#151821', Theme::dark()->backgroundColor);
        self::assertSame('#8cc63f', Theme::alto()->colorAt(0));
        self::assertSame('#1f2937', Theme::mono()->colorAt(0));
        self::assertSame('#fffaf4', Theme::warm()->backgroundColor);
    }

    #[Test]
    public function cyclesThroughPaletteColors(): void
    {
        $theme = Theme::default();

        self::assertSame($theme->colorAt(0), $theme->colorAt(count($theme->palette)));
        self::assertSame($theme->colorAt(count($theme->palette) - 1), $theme->colorAt(-1));
    }

    #[Test]
    public function derivesAThemeByOverridingSelectedRoles(): void
    {
        $theme = Theme::warm()->with(textColor: '#111111', palette: ['#ff0000']);

        self::assertSame('#111111', $theme->textColor);
        self::assertSame(Theme::warm()->backgroundColor, $theme->backgroundColor);
        self::assertSame('#ff0000', $theme->colorAt(0));
    }

    #[Test]
    public function rejectsAnEmptyPalette(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Theme('#fff', '#111', '#555', '#ddd', '#999', []);
    }

    #[Test]
    public function rejectsAnEmptyColor(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Theme('', '#111', '#555', '#ddd', '#999', ['#555']);
    }
}
