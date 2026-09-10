<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Model;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Model\PieChart;
use Atelier\Chart\Model\Slice;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PieChart::class)]
#[UsesClass(Slice::class)]
final class PieChartTest extends TestCase
{
    #[Test]
    public function exposesMetadataTotalAndDonutIntent(): void
    {
        $chart = new PieChart(
            [new Slice('Direct', 45.0), new Slice('Search', 35.0)],
            0.58,
            'Traffic',
            'Traffic sources.',
        );

        self::assertSame(80.0, $chart->total());
        self::assertTrue($chart->isDonut());
        self::assertSame('Traffic', $chart->title());
        self::assertSame('Traffic sources.', $chart->description());
        self::assertSame(720.0, $chart->width());
        self::assertSame(420.0, $chart->height());
    }

    #[Test]
    public function rejectsDuplicateLabels(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PieChart([new Slice('Direct', 45.0), new Slice('Direct', 35.0)], 0.0, 'Traffic');
    }

    #[Test]
    public function rejectsAnExcessiveInnerRadius(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PieChart([new Slice('Direct', 45.0)], 0.8, 'Traffic');
    }

    #[Test]
    public function exposesPieIntentWithoutAnInnerRadius(): void
    {
        $chart = new PieChart([new Slice('Direct', 45.0)], 0.0, 'Traffic');

        self::assertFalse($chart->isDonut());
        self::assertNull($chart->description());
    }

    #[Test]
    public function rejectsAnEmptySliceCollection(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PieChart([], 0.0, 'Traffic');
    }

    #[Test]
    public function rejectsAnEmptyTitle(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PieChart([new Slice('Direct', 45.0)], 0.0, ' ');
    }

    #[Test]
    public function rejectsAnEmptyDescription(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PieChart([new Slice('Direct', 45.0)], 0.0, 'Traffic', ' ');
    }

    #[Test]
    public function rejectsAnInvalidSize(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PieChart([new Slice('Direct', 45.0)], 0.0, 'Traffic', chartWidth: 200.0);
    }
}
