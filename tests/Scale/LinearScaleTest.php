<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Scale;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Scale\Domain;
use Atelier\Chart\Scale\LinearScale;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(LinearScale::class)]
final class LinearScaleTest extends TestCase
{
    #[Test]
    public function createsNiceTicksAndMapsTheDomain(): void
    {
        $scale = LinearScale::forValues([0.0, 46.0], 366.0, 86.0);

        self::assertSame([0.0, 20.0, 40.0, 60.0], $scale->ticks());
        self::assertSame(366.0, $scale->map(0.0));
        self::assertSame(226.0, $scale->map(30.0));
        self::assertSame(86.0, $scale->map(60.0));
    }

    #[Test]
    public function includesNegativeValuesAndZero(): void
    {
        $scale = LinearScale::forValues([-12.0, 17.0], 100.0, 0.0);

        self::assertSame([-20.0, -10.0, 0.0, 10.0, 20.0], $scale->ticks());
        self::assertSame(50.0, $scale->map(0.0));
    }

    #[Test]
    public function canFitASparklineWithoutZero(): void
    {
        $scale = LinearScale::forValues([14.0, 42.0], 56.0, 8.0, 4, false);

        self::assertGreaterThan(8.0, $scale->map(42.0));
        self::assertLessThan(56.0, $scale->map(14.0));
    }

    #[Test]
    public function expandsAConstantDomain(): void
    {
        $scale = LinearScale::forValues([5.0, 5.0], 100.0, 0.0, includeZero: false);

        self::assertLessThan(5.0, $scale->domainMin);
        self::assertGreaterThan(5.0, $scale->domainMax);
    }

    #[Test]
    public function rejectsTooFewTicks(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LinearScale::forValues([1.0], 100.0, 0.0, 1);
    }

    #[Test]
    public function preservesAnExactFixedDomain(): void
    {
        $scale = LinearScale::forValues([12.0, 18.0], 0.0, 100.0, domain: Domain::fixed(10.0, 21.0));

        self::assertSame(10.0, $scale->domainMin);
        self::assertSame(21.0, $scale->domainMax);
        self::assertSame([10.0, 15.0, 20.0, 21.0], $scale->ticks());
    }

    #[Test]
    public function rejectsInvalidRanges(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LinearScale::forValues([1.0], 10.0, 10.0);
    }

    #[Test]
    public function rejectsNonFiniteValues(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LinearScale::forValues([INF], 0.0, 10.0);
    }

    #[Test]
    public function rejectsMappingNonFiniteValues(): void
    {
        $scale = LinearScale::forValues([1.0, 2.0], 0.0, 10.0);
        $this->expectException(InvalidArgumentException::class);

        $scale->map(NAN);
    }

    #[Test]
    public function rejectsValuesOutsideAFixedDomain(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LinearScale::forValues([9.0, 18.0], 0.0, 100.0, domain: Domain::fixed(10.0, 20.0));
    }

    #[Test]
    public function preservesDistinctTicksForSmallDomains(): void
    {
        foreach ([null, Domain::fixed(1.1e-15, 3.1e-15)] as $domain) {
            $scale = LinearScale::forValues([1.2e-15, 3.0e-15], 0.0, 100.0, includeZero: false, domain: $domain);
            $ticks = $scale->ticks();
            self::assertGreaterThanOrEqual(4, count($ticks));
            foreach ($ticks as $index => $tick) {
                self::assertGreaterThanOrEqual($scale->domainMin, $tick);
                self::assertLessThanOrEqual($scale->domainMax, $tick);
                if (0 < $index) {
                    self::assertGreaterThan($ticks[$index - 1], $tick);
                }
            }
        }
    }

    #[Test]
    public function rejectsUnrepresentableDomainsAndRanges(): void
    {
        $cases = [
            [[-1.0e308, 1.0e308], 0.0, 100.0],
            [[0.0, 5.0e-324], 0.0, 100.0],
            [[1.0], -1.0e308, 1.0e308],
        ];
        foreach ($cases as [$values, $start, $end]) {
            try {
                LinearScale::forValues($values, $start, $end);
                self::fail('An unrepresentable scale must be rejected.');
            } catch (InvalidArgumentException $exception) {
                self::assertNotSame('', $exception->getMessage());
            }
        }
    }

    #[Test]
    public function emitsOnlyDistinctTicksForANarrowDomainAtALargeOffset(): void
    {
        $scale = LinearScale::forValues([1.0e16, 1.0e16 + 2.0], 0.0, 100.0, domain: Domain::fixed(1.0e16, 1.0e16 + 2.0));

        self::assertSame([1.0e16, 1.0e16 + 2.0], $scale->ticks());
    }
}
