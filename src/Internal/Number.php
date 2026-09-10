<?php

declare(strict_types=1);

namespace Atelier\Chart\Internal;

/** @internal */
final class Number
{
    /** Serialize a float losslessly without depending on the host's JSON precision. */
    public static function serialize(float $value): string
    {
        $previous = ini_set('serialize_precision', '-1');

        try {
            return json_encode($value, JSON_THROW_ON_ERROR);
        } finally {
            if (false !== $previous) {
                ini_set('serialize_precision', $previous);
            }
        }
    }
}
