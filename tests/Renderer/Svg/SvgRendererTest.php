<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Renderer\Svg;

use Atelier\Chart\Chart;
use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\ActivityCell;
use Atelier\Chart\Model\ActivityChart;
use Atelier\Chart\Model\CartesianChart;
use Atelier\Chart\Model\ChartKind;
use Atelier\Chart\Model\ChartModel;
use Atelier\Chart\Model\DivergingBarChart;
use Atelier\Chart\Model\Gauge;
use Atelier\Chart\Model\PieChart;
use Atelier\Chart\Model\Point;
use Atelier\Chart\Model\PointChart;
use Atelier\Chart\Model\PointSeries;
use Atelier\Chart\Model\Series;
use Atelier\Chart\Model\Slice;
use Atelier\Chart\Model\Sparkline;
use Atelier\Chart\Model\StackedBarChart;
use Atelier\Chart\Model\StripChart;
use Atelier\Chart\Model\StripSegment;
use Atelier\Chart\Renderer\Svg\SvgRenderer;
use Atelier\Chart\Renderer\Svg\SvgRenderOptions;
use Atelier\Chart\Scale\LinearScale;
use Atelier\Chart\Theme\Theme;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SvgRenderer::class)]
#[UsesClass(CartesianChart::class)]
#[UsesClass(DivergingBarChart::class)]
#[UsesClass(Gauge::class)]
#[UsesClass(ActivityCell::class)]
#[UsesClass(ActivityChart::class)]
#[UsesClass(PieChart::class)]
#[UsesClass(Point::class)]
#[UsesClass(PointChart::class)]
#[UsesClass(PointSeries::class)]
#[UsesClass(Slice::class)]
#[UsesClass(Sparkline::class)]
#[UsesClass(StackedBarChart::class)]
#[UsesClass(StripChart::class)]
#[UsesClass(StripSegment::class)]
#[UsesClass(LinearScale::class)]
final class SvgRendererTest extends TestCase
{
    #[Test]
    public function roundsGroupedBarWidthsConsistentlyAcrossPhpVersions(): void
    {
        $chart = Chart::bar()
            ->title('Quarterly revenue')
            ->series('2025', ['Q1' => 18, 'Q2' => 32, 'Q3' => 24, 'Q4' => 42])
            ->series('2026', ['Q1' => 26, 'Q2' => 24, 'Q3' => 38, 'Q4' => 35])
            ->build();

        $xml = new \DOMDocument();
        $xml->loadXML(Chart::render($chart, options: new SvgRenderOptions(dataAttributes: true)));
        $bars = (new \DOMXPath($xml))->query('//*[local-name()="rect"][@data-series]');
        self::assertNotFalse($bars);
        self::assertCount(8, $bars);
        foreach ($bars as $bar) {
            self::assertInstanceOf(\DOMElement::class, $bar);
            self::assertSame('54.91', $bar->getAttribute('width'));
        }
    }

    #[Test]
    public function rendersAccessibleGroupedBarsWithDataAttributes(): void
    {
        $chart = Chart::bar()
            ->title('Revenue')
            ->description('Two years.')
            ->series('2025', ['Q1' => 18, 'Q2' => 27])
            ->series('2026', ['Q1' => 24, 'Q2' => 31])
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::default());

        self::assertStringContainsString('role="img"', $svg);
        self::assertStringContainsString('role="img" aria-label="Revenue"', $svg);
        self::assertStringContainsString('<title>Revenue</title>', $svg);
        self::assertStringContainsString('<desc>Two years. Chart data.', $svg);
        self::assertStringNotContainsString(' id=', $svg);
        self::assertSame(4, substr_count($svg, 'class="atelier-chart__bar"'));
        self::assertStringContainsString('data-series="2025" data-category="Q1" data-value="18"', $svg);
        self::assertStringContainsString('class="atelier-chart__tick-label">20</text>', $svg);
    }

    #[Test]
    public function rendersMultipleInlineChartsWithoutCollidingAccessibilityIds(): void
    {
        $chart = new CartesianChart(
            ChartKind::Bar,
            ['Q1'],
            [new Series('2025', [18.0])],
            'Revenue',
        );

        $renderer = new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true));
        $html = $renderer->render($chart, Theme::default()).$renderer->render($chart, Theme::default());

        self::assertSame(0, substr_count($html, ' id='));
        self::assertSame(2, substr_count($html, '<desc>Chart data.'));
    }

    #[Test]
    public function rejectsLegendsThatCannotFitTheirCanvas(): void
    {
        $models = [
            Chart::pie()->size(280, 240)->slice(str_repeat('Wide label ', 5), 1)->build(),
            Chart::gauge(50)->size(240, 160)->label('An exceptionally long current value')->series('Target', 60)->build(),
            Chart::strip()->size(320, 160)->segment(1, str_repeat('Wide label ', 5))->build(),
            Chart::radar()->size(240, 180)
                ->series('One long legend', ['A' => 1, 'B' => 2, 'C' => 3])
                ->series('Two long legend', ['A' => 2, 'B' => 3, 'C' => 4])
                ->build(),
        ];
        $rejected = 0;

        foreach ($models as $model) {
            try {
                (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($model, Theme::default());
            } catch (InvalidArgumentException) {
                ++$rejected;
            }
        }

        self::assertSame(count($models), $rejected);
    }

    #[Test]
    public function rendersNegativeBarsAroundTheZeroBaseline(): void
    {
        $chart = new CartesianChart(
            ChartKind::Bar,
            ['Loss', 'Gain'],
            [new Series('Change', [-12.0, 17.0])],
            'Change',
        );

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::default());

        self::assertStringContainsString('data-value="-12"', $svg);
        self::assertStringContainsString('class="atelier-chart__baseline"', $svg);
        self::assertSame(2, substr_count($svg, 'class="atelier-chart__bar"'));
    }

    #[Test]
    public function rendersLinesAndPointGeometry(): void
    {
        $chart = Chart::line()
            ->series('Trend', ['Mon' => 1, 'Tue' => 3, 'Wed' => 2])
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::dark());

        self::assertSame(1, substr_count($svg, 'class="atelier-chart__line"'));
        self::assertSame(3, substr_count($svg, 'class="atelier-chart__point"'));
        self::assertStringContainsString('fill="#151821"', $svg);
    }

    #[Test]
    public function rendersAnAreaBelowItsLine(): void
    {
        $chart = Chart::area()
            ->series('Visits', ['01' => 12, '02' => 17, '03' => 14])
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::warm());

        self::assertSame(1, substr_count($svg, 'class="atelier-chart__area"'));
        self::assertSame(1, substr_count($svg, 'class="atelier-chart__line"'));
        self::assertLessThan(
            strpos($svg, 'class="atelier-chart__line"'),
            strpos($svg, 'class="atelier-chart__area"'),
        );
    }

    #[Test]
    public function rendersRadarGeometryAndSeriesMetadata(): void
    {
        $chart = Chart::radar()
            ->series('Current', ['Speed' => 72, 'Quality' => 84, 'Reach' => 66, 'Trust' => 90])
            ->series('Target', ['Speed' => 85, 'Quality' => 92, 'Reach' => 80, 'Trust' => 95])
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::warm());

        self::assertSame(5, substr_count($svg, 'class="atelier-chart__radar-grid"'));
        self::assertSame(5, substr_count($svg, 'class="atelier-chart__radar-level"'));
        self::assertSame(4, substr_count($svg, 'class="atelier-chart__radar-axis"'));
        self::assertSame(1, substr_count($svg, 'class="atelier-chart__radar-center"'));
        self::assertSame(2, substr_count($svg, 'class="atelier-chart__radar-series"'));
        self::assertSame(8, substr_count($svg, 'class="atelier-chart__radar-point"'));
        self::assertStringContainsString('data-series="Current"', $svg);
        self::assertSame(2, substr_count($svg, 'class="atelier-chart__radar-series" stroke='));
        self::assertStringContainsString('stroke-dasharray="8 6"', $svg);
    }

    #[Test]
    public function rendersASmoothLineThatStillPassesThroughEveryPoint(): void
    {
        $chart = Chart::line()
            ->smooth()
            ->series('Trend', ['Mon' => 12, 'Tue' => 18, 'Wed' => 14, 'Thu' => 24])
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::default());

        self::assertSame(3, substr_count($svg, ' C '));
        self::assertSame(4, substr_count($svg, 'class="atelier-chart__point"'));
    }

    #[Test]
    public function rendersAThemedGaugeWithBounds(): void
    {
        $chart = Chart::gauge(72.0)
            ->range(0.0, 100.0)
            ->unit('%')
            ->color('#123456')
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::dark());

        self::assertSame(1, substr_count($svg, 'class="atelier-chart__gauge-track"'));
        self::assertSame(1, substr_count($svg, 'class="atelier-chart__gauge-value"'));
        self::assertStringContainsString('stroke="#123456"', $svg);
        self::assertStringNotContainsString(' 0 1 1 ', $svg);
        self::assertStringContainsString('class="atelier-chart__gauge-label">72%</text>', $svg);
        self::assertSame(2, substr_count($svg, 'class="atelier-chart__gauge-bound"'));
    }

    #[Test]
    public function omitsGaugeValueArcAtItsMinimum(): void
    {
        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render(Chart::gauge(0.0)->build(), Theme::default());

        self::assertSame(0, substr_count($svg, 'class="atelier-chart__gauge-value"'));
    }

    #[Test]
    public function rendersAThreeSeriesGaugeAsConcentricArcs(): void
    {
        $chart = Chart::gauge(78.0)
            ->label('Overall')
            ->series('Quality', 86.0)
            ->series('Readiness', 71.0)
            ->unit('%')
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::alto());

        self::assertSame(3, substr_count($svg, 'class="atelier-chart__gauge-track"'));
        self::assertSame(3, substr_count($svg, 'class="atelier-chart__gauge-value"'));
        self::assertSame(3, substr_count($svg, 'class="atelier-chart__gauge-series-label"'));
        self::assertStringContainsString('Overall 78%', $svg);
        self::assertStringContainsString('Quality 86%', $svg);
        self::assertStringContainsString('Readiness 71%', $svg);
    }

    #[Test]
    public function rendersPieSlicesWithExplicitPercentages(): void
    {
        $chart = Chart::pie()
            ->title('Traffic sources')
            ->slice('Direct', 45.0)
            ->slice('Search', 35.0)
            ->slice('Partners', 20.0)
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::alto());

        self::assertSame(3, substr_count($svg, 'class="atelier-chart__pie-slice"'));
        self::assertSame(3, substr_count($svg, 'class="atelier-chart__pie-value"'));
        self::assertStringContainsString('data-label="Direct" data-value="45" data-ratio="0.45"', $svg);
        self::assertStringContainsString('class="atelier-chart__pie-value">45%</text>', $svg);
        self::assertStringNotContainsString('atelier-chart__donut-total', $svg);
    }

    #[Test]
    public function rendersAFullDonutWithoutCollapsingItsArc(): void
    {
        $chart = Chart::donut()
            ->slice('Complete', 100.0)
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::default());

        self::assertSame(1, substr_count($svg, 'class="atelier-chart__pie-slice"'));
        self::assertStringContainsString('class="atelier-chart__donut-total">100</text>', $svg);
        self::assertGreaterThanOrEqual(4, substr_count($svg, ' A '));
    }

    #[Test]
    public function rendersANarrowFullPieWithACompactLegend(): void
    {
        $chart = Chart::pie()
            ->size(320.0, 300.0)
            ->slice('Complete', 100.0)
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::default());

        self::assertSame(1, substr_count($svg, 'class="atelier-chart__pie-slice"'));
        self::assertStringContainsString('class="atelier-chart__pie-label">Complete</text>', $svg);
        self::assertGreaterThanOrEqual(2, substr_count($svg, ' A '));
    }

    #[Test]
    public function rendersPlotPointsAgainstTwoNumericAxes(): void
    {
        $chart = Chart::scatter()
            ->axes('Age', 'Spend')
            ->series('New', [['x' => 20, 'y' => 80], ['x' => 35, 'y' => 55]])
            ->series('Returning', [['x' => 50, 'y' => 70]])
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::dark());

        self::assertSame(3, substr_count($svg, 'class="atelier-chart__plot-point"'));
        self::assertSame(2, substr_count($svg, 'class="atelier-chart__axis-label"'));
        self::assertStringContainsString('data-series="New" data-x="20" data-y="80" data-size="1"', $svg);
    }

    #[Test]
    public function rendersBubbleAreasFromPointSizes(): void
    {
        $chart = Chart::bubble()
            ->series('Markets', [
                ['x' => 20, 'y' => 80, 'size' => 25],
                ['x' => 35, 'y' => 55, 'size' => 400],
            ])
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::warm());

        self::assertSame(2, substr_count($svg, 'class="atelier-chart__bubble-point"'));
        self::assertStringContainsString('r="6.75"', $svg);
        self::assertStringContainsString('r="27"', $svg);
        self::assertStringContainsString('data-size="400"', $svg);
    }

    #[Test]
    public function rendersVerticalStackedSegmentsAndLabels(): void
    {
        $chart = Chart::stackedBar()
            ->showValues()
            ->series('Product', ['Q1' => 40, 'Q2' => 50])
            ->series('Services', ['Q1' => 20, 'Q2' => 30])
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::default());

        self::assertSame(4, substr_count($svg, 'class="atelier-chart__stacked-segment"'));
        self::assertStringContainsString('data-series="Services" data-category="Q2" data-value="30"', $svg);
        self::assertStringContainsString('class="atelier-chart__stacked-value">40</text>', $svg);
        self::assertSame(2, substr_count($svg, 'class="atelier-chart__category-label"'));
    }

    #[Test]
    public function rendersHorizontalStackedSegments(): void
    {
        $chart = Chart::stackedBar()
            ->horizontal()
            ->series('Done', ['Core' => 40, 'Web' => 30])
            ->series('Next', ['Core' => 20, 'Web' => 35])
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::mono());

        self::assertSame(4, substr_count($svg, 'class="atelier-chart__stacked-segment"'));
        self::assertStringContainsString('class="atelier-chart__category-label">Core</text>', $svg);
        self::assertStringContainsString('class="atelier-chart__baseline"', $svg);
    }

    #[Test]
    public function rendersVerticalDivergingBarsAroundZero(): void
    {
        $chart = Chart::divergingBar()
            ->series('2024', ['Jan' => 21, 'Feb' => 10, 'Mar' => 13])
            ->series('2023', ['Jan' => 13, 'Feb' => 16, 'Mar' => 14])
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::default());

        self::assertSame(6, substr_count($svg, 'class="atelier-chart__diverging-bar"'));
        self::assertSame(1, substr_count($svg, 'class="atelier-chart__baseline"'));
        self::assertStringContainsString('data-series="2023" data-category="Feb" data-value="-16"', $svg);
        self::assertSame(3, substr_count($svg, 'class="atelier-chart__category-label"'));
    }

    #[Test]
    public function rendersAHorizontalPopulationPyramid(): void
    {
        $chart = Chart::divergingBar()
            ->horizontal()
            ->showValues()
            ->series('Women', ['0-9' => 18, '10-19' => 22])
            ->series('Men', ['0-9' => 20, '10-19' => 19])
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::mono());

        self::assertSame(4, substr_count($svg, 'class="atelier-chart__diverging-bar"'));
        self::assertSame(4, substr_count($svg, 'class="atelier-chart__value-label"'));
        self::assertStringContainsString('class="atelier-chart__category-label">0-9</text>', $svg);
    }

    #[Test]
    public function rendersACompactSparklineWithItsEndpoint(): void
    {
        $chart = new Sparkline([14.0, 18.0, 16.0, 24.0], 'Trend', color: '#123456');

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::default());

        self::assertStringContainsString('viewBox="0 0 240 64"', $svg);
        self::assertStringContainsString('class="atelier-chart__sparkline-guide"', $svg);
        self::assertStringContainsString('class="atelier-chart__sparkline-area"', $svg);
        self::assertStringContainsString('class="atelier-chart__sparkline" stroke="#123456"', $svg);
        self::assertStringContainsString('class="atelier-chart__sparkline-start"', $svg);
        self::assertStringContainsString('class="atelier-chart__sparkline-end"', $svg);
        self::assertSame(2, substr_count($svg, 'class="atelier-chart__sparkline-value"'));
    }

    #[Test]
    public function rendersActivityAsASevenRowIntensityMatrix(): void
    {
        $values = [];

        for ($index = 1; $index <= 15; ++$index) {
            $values['D'.$index] = $index % 5;
        }

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render(Chart::activity()->series('Work', $values)->build(), Theme::default());

        self::assertSame(15, substr_count($svg, 'class="atelier-chart__activity-cell"'));
        self::assertSame(7, substr_count($svg, 'class="atelier-chart__activity-day"'));
        self::assertSame(4, substr_count($svg, 'class="atelier-chart__activity-legend-step"'));
        self::assertStringContainsString('data-series="Work" data-category="D15" data-value="0"', $svg);
    }

    #[Test]
    public function rendersZeroActivityWithoutDividingByZero(): void
    {
        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render(Chart::activity()->series('Work', ['Mon' => 0])->build(), Theme::mono());

        self::assertStringContainsString('class="atelier-chart__activity-cell"', $svg);
        self::assertStringNotContainsString('NAN', strtoupper($svg));
    }

    #[Test]
    public function rendersAStyledActivityStrip(): void
    {
        $chart = Chart::activity()
            ->strip()
            ->summary('99.9% available')
            ->series('Availability', [
                'Yesterday' => ['value' => 1, 'color' => '#22c55e', 'label' => 'Available'],
                'Incident' => ['value' => 1, 'color' => '#ef4444', 'label' => 'Unavailable'],
                'Today' => ['value' => 1, 'color' => '#22c55e', 'label' => 'Available'],
            ])
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::default());

        self::assertSame(3, substr_count($svg, 'atelier-chart__activity-cell--strip'));
        self::assertStringContainsString('class="atelier-chart__activity-current">Available</text>', $svg);
        self::assertStringContainsString('class="atelier-chart__activity-summary">99.9% available</text>', $svg);
        self::assertStringContainsString('data-category="Incident" data-value="1" data-label="Unavailable"', $svg);
        self::assertStringNotContainsString('data-status=', $svg);
    }

    #[Test]
    public function rendersProportionalSegmentsInInputOrder(): void
    {
        $chart = Chart::strip()->size(720, 240)
            ->segment(weight: 12, label: 'Production', color: '#06bfa8')
            ->segment(weight: 3, label: 'Pause', color: '#f4a34b')
            ->segment(weight: 9, label: 'Production', color: '#06bfa8')
            ->build();
        $svg = self::renderWithHooks($chart);
        $xml = new \SimpleXMLElement($svg);
        $segments = $xml->xpath('//*[@class="atelier-chart__strip-segment"]');
        self::assertCount(3, $segments);
        self::assertSame(['Production', 'Pause', 'Production'], array_map(static fn ($node): string => (string) $node['data-label'], $segments));
        self::assertSame('12', (string) $segments[0]['data-weight']);
        self::assertSame('0.125', (string) $segments[1]['data-ratio']);
        self::assertEqualsWithDelta(332, (float) $segments[0]['width'], 0.001);
        self::assertEqualsWithDelta(83, (float) $segments[1]['width'], 0.001);
        self::assertEqualsWithDelta(360, (float) $segments[1]['x'], 0.001);
        self::assertEqualsWithDelta(692, (float) $segments[2]['x'] + (float) $segments[2]['width'], 0.001);
        self::assertSame(2, substr_count($svg, 'class="atelier-chart__strip-legend-label"'));
        self::assertStringNotContainsString('available', $svg);
        self::assertStringNotContainsString('data-state=', $svg);
        self::assertStringNotContainsString('data-duration=', $svg);
    }

    #[Test]
    public function assignsPaletteColorsByLabelAndPreservesExplicitOverrides(): void
    {
        $svg = self::renderWithHooks(Chart::strip()
            ->segment(1, 'A')->segment(2, 'B')->segment(3, 'A')->segment(1, 'A', '#ff0000')
            ->build(), Theme::mono());
        $xml = new \SimpleXMLElement($svg);
        $segments = $xml->xpath('//*[@class="atelier-chart__strip-segment"]');
        self::assertSame(Theme::mono()->colorAt(0), (string) $segments[0]['fill']);
        self::assertSame(Theme::mono()->colorAt(1), (string) $segments[1]['fill']);
        self::assertSame((string) $segments[0]['fill'], (string) $segments[2]['fill']);
        self::assertSame('#ff0000', (string) $segments[3]['fill']);
        self::assertSame(3, substr_count($svg, 'class="atelier-chart__strip-legend-label"'));
    }

    #[Test]
    public function omitsTheStripLegendOnRequest(): void
    {
        $svg = self::renderWithHooks(Chart::strip()->withoutLegend()->segment(1, 'A')->build());
        self::assertStringNotContainsString('atelier-chart__strip-legend', $svg);
        self::assertStringContainsString('data-weight="1"', $svg);
    }

    #[Test]
    public function wrapsTheStripLegendOnNarrowCanvases(): void
    {
        $builder = Chart::strip()->size(320, 240);
        foreach (['A', 'B', 'C'] as $label) {
            $builder->segment(1, $label);
        }
        $xml = new \SimpleXMLElement(self::renderWithHooks($builder->build()));
        $labels = $xml->xpath('//*[@class="atelier-chart__strip-legend-label"]');
        self::assertCount(3, $labels);
        self::assertEqualsWithDelta(22, (float) $labels[1]['y'] - (float) $labels[0]['y'], 0.001);
        self::assertLessThan(240, (float) $labels[2]['y']);
    }

    #[Test]
    public function rejectsAStripLegendThatIsTooTall(): void
    {
        $this->expectException(InvalidArgumentException::class);
        self::renderWithHooks(Chart::strip()->size(320, 160)->segment(1, 'A')->segment(1, 'B')->segment(1, 'C')->build());
    }

    #[Test]
    public function preservesSmallWeightsAndScaleInvariance(): void
    {
        $render = static fn (float $scale): string => self::renderWithHooks(Chart::strip()->withoutLegend()
            ->segment(0.001 * $scale, 'Small')->segment(0.999 * $scale, 'Large')->build());
        $one = new \SimpleXMLElement($render(1));
        $many = new \SimpleXMLElement($render(1000));
        $first = $one->xpath('//*[@class="atelier-chart__strip-segment"]')[0];
        $scaled = $many->xpath('//*[@class="atelier-chart__strip-segment"]')[0];
        self::assertEqualsWithDelta(0.664, (float) $first['width'], 0.001);
        self::assertEqualsWithDelta((float) $first['width'], (float) $scaled['width'], 0.001);
    }

    #[Test]
    public function formatsLargeValueLabelsCompactly(): void
    {
        $chart = Chart::bar()
            ->series('Revenue', ['Total' => 1_200_000])
            ->build();

        $svg = (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($chart, Theme::default());

        self::assertStringContainsString('class="atelier-chart__value-label">1.2M</text>', $svg);
    }

    #[Test]
    public function rejectsAnUnknownChartModel(): void
    {
        $model = new class implements ChartModel {
            public function width(): float
            {
                return 100.0;
            }

            public function height(): float
            {
                return 100.0;
            }

            public function title(): string
            {
                return 'Unknown';
            }

            public function description(): ?string
            {
                return null;
            }
        };

        $this->expectException(InvalidArgumentException::class);

        (new SvgRenderer(options: new SvgRenderOptions(classes: true, dataAttributes: true)))->render($model, Theme::default());
    }

    private static function renderWithHooks(ChartModel $model, ?Theme $theme = null): string
    {
        return Chart::render($model, $theme, options: new SvgRenderOptions(classes: true, dataAttributes: true));
    }

    #[Test]
    public function preservesDataAndAccessibleValuesDespiteHostSerializationPrecision(): void
    {
        $previous = ini_set('serialize_precision', '3');
        try {
            $model = Chart::bar()->series('Measurements', ['Sample' => 1.23456789])->build();
            $svg = Chart::render($model, options: new SvgRenderOptions(dataAttributes: true));
            self::assertStringContainsString('data-value="1.23456789"', $svg);
            self::assertStringContainsString('Sample 1.23456789', $svg);
            self::assertSame('3', ini_get('serialize_precision'));
        } finally {
            if (false !== $previous) {
                ini_set('serialize_precision', $previous);
            }
        }
    }

    #[Test]
    public function keepsBarsAndAreaBaselinesInsideFixedDomainsThatExcludeZero(): void
    {
        foreach ([Chart::bar(), Chart::area()] as $builder) {
            $model = $builder->domain(90.0, 110.0)->series('Level', ['A' => 95, 'B' => 105])->build();
            $svg = Chart::render($model, options: new SvgRenderOptions(classes: true));
            $document = new \DOMDocument();
            self::assertTrue($document->loadXML($svg));
            $xpath = new \DOMXPath($document);
            $bars = $xpath->query('//*[@class="atelier-chart__bar"]');
            self::assertNotFalse($bars);
            foreach ($bars as $bar) {
                self::assertInstanceOf(\DOMElement::class, $bar);
                self::assertLessThanOrEqual($model->height(), (float) $bar->getAttribute('y') + (float) $bar->getAttribute('height'));
            }
            $areas = $xpath->query('//*[@class="atelier-chart__area"]');
            self::assertNotFalse($areas);
            foreach ($areas as $area) {
                self::assertInstanceOf(\DOMElement::class, $area);
                preg_match_all('/[-+]?[0-9]*\.?[0-9]+/', $area->getAttribute('d'), $coordinates);
                foreach ($coordinates[0] as $index => $coordinate) {
                    self::assertGreaterThanOrEqual(0.0, (float) $coordinate);
                    self::assertLessThanOrEqual(0 === $index % 2 ? $model->width() : $model->height(), (float) $coordinate);
                }
            }
        }
    }
}
