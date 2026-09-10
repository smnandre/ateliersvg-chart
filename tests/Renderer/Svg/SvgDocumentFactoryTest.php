<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Renderer\Svg;

use Atelier\Chart\Accessibility\DataSummary;
use Atelier\Chart\Chart;
use Atelier\Chart\Model\CartesianChart;
use Atelier\Chart\Renderer\Svg\SvgDocumentFactory;
use Atelier\Chart\Theme\Theme;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SvgDocumentFactory::class)]
#[UsesClass(DataSummary::class)]
#[UsesClass(CartesianChart::class)]
final class SvgDocumentFactoryTest extends TestCase
{
    #[Test]
    public function createsAnAccessibleIdFreeDocument(): void
    {
        $chart = Chart::bar()->description('Quarterly values.')->series('A', ['Q1' => 1])->build();
        $root = (new SvgDocumentFactory())->create($chart, Theme::default())->getSvg();

        self::assertSame('img', $root->getAttribute('role'));
        self::assertSame('Bar chart', $root->getAttribute('aria-label'));
        self::assertNull($root->getAttribute('id'));
    }
}
