<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Internal;

use Atelier\Chart\Exception\InvalidArgumentException;
use Atelier\Chart\Internal\Input;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Input::class)]
final class InputTest extends TestCase
{
    #[Test]
    public function acceptsOnlyFiniteRuntimeNumbers(): void
    {
        self::assertSame(12.0, Input::number(12, 'Value'));

        $this->expectException(InvalidArgumentException::class);
        Input::number('12', 'Value');
    }

    #[Test]
    public function rejectsOverflowingTotals(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Input::finiteSum([PHP_FLOAT_MAX, PHP_FLOAT_MAX], 'Values');
    }

    #[Test]
    public function validatesDimensionsAndFiniteSums(): void
    {
        Input::dimensions(320.0, 240.0, 240.0, 180.0, 'Chart');

        self::assertSame(3.0, Input::finiteSum([1.0, 2.0], 'Values'));
    }

    #[Test]
    public function rejectsNonFiniteNumbers(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Input::number(INF, 'Value');
    }

    #[Test]
    public function rejectsNonFiniteDimensions(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Input::dimensions(NAN, 240.0, 240.0, 180.0, 'Chart');
    }
}
