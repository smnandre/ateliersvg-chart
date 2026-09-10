<?php

declare(strict_types=1);

namespace Atelier\Chart\Tests\Exception;

use Atelier\Chart\Exception\ExceptionInterface;
use Atelier\Chart\Exception\InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidArgumentException::class)]
final class InvalidArgumentExceptionTest extends TestCase
{
    #[Test]
    public function belongsToThePackageExceptionHierarchy(): void
    {
        self::assertInstanceOf(ExceptionInterface::class, new InvalidArgumentException('Invalid chart.'));
    }
}
