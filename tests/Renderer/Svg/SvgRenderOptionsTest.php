<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Renderer\Svg;

use Atelier\Chart\Chart;
use Atelier\Chart\Model\ChartModel;
use Atelier\Chart\Renderer\Svg\SvgDocumentFactory;
use Atelier\Chart\Renderer\Svg\SvgRenderer;
use Atelier\Chart\Renderer\Svg\SvgRenderOptions;
use Atelier\Chart\Theme\Theme;
use Atelier\Svg\Dumper\CompactXmlDumper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(SvgRenderOptions::class)]
#[CoversClass(SvgRenderer::class)]
#[CoversClass(SvgDocumentFactory::class)]
#[CoversClass(Chart::class)]
final class SvgRenderOptionsTest extends TestCase
{
    /** @return iterable<string, array{ChartModel}> */
    public static function charts(): iterable
    {
        foreach (['bar', 'line', 'area', 'radar', 'stackedBar', 'divergingBar'] as $kind) {
            yield $kind => [Chart::$kind()->series('A', ['One' => 2, 'Two' => 3, 'Three' => 4])
                ->series('B', ['One' => 4, 'Two' => 1, 'Three' => 2])->build()];
        }
        foreach (['pie', 'donut'] as $kind) {
            yield $kind => [Chart::$kind()->slice('A', 2)->slice('B', 3)->build()];
        }
        foreach (['scatter', 'bubble'] as $kind) {
            yield $kind => [Chart::$kind()->series('A', [['x' => 1, 'y' => 2, 'size' => 3], ['x' => 2, 'y' => 4, 'size' => 5]])->build()];
        }
        yield 'gauge' => [Chart::gauge(45)->build()];
        yield 'sparkline' => [Chart::sparkline([1, 3, 2])->build()];
        yield 'activity' => [Chart::activity()->series('A', ['Mon' => 1, 'Tue' => 2])->build()];
        yield 'strip' => [Chart::strip()->segment(2, 'A')->segment(1, 'B')->build()];
    }

    #[Test]
    #[DataProvider('charts')]
    public function keepsAllChartFamiliesAccessibleWithoutHooksByDefault(ChartModel $model): void
    {
        $svg = Chart::render($model);
        $xml = new \SimpleXMLElement($svg);

        self::assertSame([], $xml->xpath('//@class | //@*[starts-with(name(), "data-")] | //@style'));
        self::assertSame('img', (string) $xml['role']);
        self::assertSame($model->title(), (string) $xml['aria-label']);
        self::assertSame($model->title(), (string) $xml->title);
        self::assertStringContainsString('Chart data.', (string) $xml->desc);
        self::assertCount(1, $xml->xpath('//@font-family'));
        self::assertSame(Theme::default()->fontFamily, (string) $xml['font-family']);
        self::assertSame($svg, (new CompactXmlDumper())->dump(Chart::renderDocument($model)));

        foreach ([new SvgRenderOptions(classes: true), new SvgRenderOptions(dataAttributes: true), new SvgRenderOptions(true, true)] as $options) {
            $withHooks = Chart::render($model, options: $options);
            self::assertSame($options->classes, str_contains($withHooks, 'class="atelier-chart'));
            self::assertSame($options->dataAttributes, str_contains($withHooks, 'data-renderer='));
            // Removing the explicitly requested hooks recovers exactly the same geometry and styling.
            self::assertSame($svg, preg_replace('/ (?:class|data-[a-z-]+)="[^"]*"/', '', $withHooks));
            self::assertSame($withHooks, (new CompactXmlDumper())->dump(Chart::renderDocument($model, options: $options)));
        }
    }

    #[Test]
    public function groupsAdjacentLabelsAndPreservesTheirOwnCoordinates(): void
    {
        $model = Chart::bar()->series('A', ['One' => 1, 'Two' => 2, 'Three' => 3])->build();
        $xml = new \SimpleXMLElement(Chart::render($model, Theme::warm()));
        $groups = $xml->xpath('//*[local-name()="g"][*[local-name()="text"][text()="One"]]');
        self::assertCount(1, $groups);
        $group = $groups[0];
        self::assertSame(Theme::warm()->mutedTextColor, (string) $group['fill']);
        self::assertSame('middle', (string) $group['text-anchor']);
        self::assertSame('11', (string) $group['font-size']);
        self::assertSame('500', (string) $group['font-weight']);
        $labels = $group->xpath('./*[local-name()="text"]');
        self::assertSame(['One', 'Two', 'Three'], array_map(static fn ($label): string => (string) $label, $labels));
        foreach ($labels as $label) {
            self::assertNotSame('', (string) $label['x']);
            self::assertNotSame('', (string) $label['y']);
            foreach (['fill', 'font-family', 'font-size', 'font-weight', 'text-anchor'] as $attribute) {
                self::assertFalse(isset($label[$attribute]));
            }
        }
    }
}
