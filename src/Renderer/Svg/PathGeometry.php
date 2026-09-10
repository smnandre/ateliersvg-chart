<?php

declare(strict_types=1);

namespace Atelier\Chart\Renderer\Svg;

final class PathGeometry
{
    public function radarPolygon(float $centerX, float $centerY, float $radius, int $axisCount): string
    {
        $path = '';

        for ($index = 0; $index < $axisCount; ++$index) {
            $angle = -M_PI / 2.0 + 2.0 * M_PI * $index / $axisCount;
            $x = $centerX + $radius * cos($angle);
            $y = $centerY + $radius * sin($angle);
            $path .= (0 === $index ? 'M ' : ' L ').$this->format($x).' '.$this->format($y);
        }

        return $path.' Z';
    }

    public function pieSlice(float $centerX, float $centerY, float $outerRadius, float $innerRadius, float $startAngle, float $endAngle): string
    {
        $outerStartX = $centerX + $outerRadius * cos($startAngle);
        $outerStartY = $centerY + $outerRadius * sin($startAngle);
        $outerEndX = $centerX + $outerRadius * cos($endAngle);
        $outerEndY = $centerY + $outerRadius * sin($endAngle);
        $fullCircle = $endAngle - $startAngle >= 2.0 * M_PI - 1e-9;

        if ($fullCircle) {
            $outerMidX = $centerX + $outerRadius * cos($startAngle + M_PI);
            $outerMidY = $centerY + $outerRadius * sin($startAngle + M_PI);
            $outer = 'M '.$this->format($outerStartX).' '.$this->format($outerStartY)
                .' A '.$this->format($outerRadius).' '.$this->format($outerRadius).' 0 1 1 '
                .$this->format($outerMidX).' '.$this->format($outerMidY)
                .' A '.$this->format($outerRadius).' '.$this->format($outerRadius).' 0 1 1 '
                .$this->format($outerStartX).' '.$this->format($outerStartY);

            if ($innerRadius <= 0.0) {
                return $outer.' Z';
            }

            $innerStartX = $centerX + $innerRadius * cos($startAngle);
            $innerStartY = $centerY + $innerRadius * sin($startAngle);
            $innerMidX = $centerX + $innerRadius * cos($startAngle + M_PI);
            $innerMidY = $centerY + $innerRadius * sin($startAngle + M_PI);

            return $outer.' L '.$this->format($innerStartX).' '.$this->format($innerStartY)
                .' A '.$this->format($innerRadius).' '.$this->format($innerRadius).' 0 1 0 '
                .$this->format($innerMidX).' '.$this->format($innerMidY)
                .' A '.$this->format($innerRadius).' '.$this->format($innerRadius).' 0 1 0 '
                .$this->format($innerStartX).' '.$this->format($innerStartY).' Z';
        }

        $largeArc = $endAngle - $startAngle > M_PI ? 1 : 0;

        if ($innerRadius <= 0.0) {
            return 'M '.$this->format($centerX).' '.$this->format($centerY)
                .' L '.$this->format($outerStartX).' '.$this->format($outerStartY)
                .' A '.$this->format($outerRadius).' '.$this->format($outerRadius).' 0 '.$largeArc.' 1 '
                .$this->format($outerEndX).' '.$this->format($outerEndY).' Z';
        }

        $innerEndX = $centerX + $innerRadius * cos($endAngle);
        $innerEndY = $centerY + $innerRadius * sin($endAngle);
        $innerStartX = $centerX + $innerRadius * cos($startAngle);
        $innerStartY = $centerY + $innerRadius * sin($startAngle);

        return 'M '.$this->format($outerStartX).' '.$this->format($outerStartY)
            .' A '.$this->format($outerRadius).' '.$this->format($outerRadius).' 0 '.$largeArc.' 1 '
            .$this->format($outerEndX).' '.$this->format($outerEndY)
            .' L '.$this->format($innerEndX).' '.$this->format($innerEndY)
            .' A '.$this->format($innerRadius).' '.$this->format($innerRadius).' 0 '.$largeArc.' 0 '
            .$this->format($innerStartX).' '.$this->format($innerStartY).' Z';
    }

    public function bubbleRadius(float $size, float $maximum): float
    {
        return 27.0 * sqrt($size / $maximum);
    }

    /** @param non-empty-list<array{0: float, 1: float, 2: float}> $points */
    public function cartesianLine(array $points, bool $smooth): string
    {
        $path = 'M '.$this->format($points[0][0]).' '.$this->format($points[0][1]);

        if (!$smooth || 2 > count($points)) {
            for ($index = 1; $index < count($points); ++$index) {
                $path .= ' L '.$this->format($points[$index][0]).' '.$this->format($points[$index][1]);
            }

            return $path;
        }

        $tangents = $this->monotoneTangents($points);

        for ($index = 0; $index < count($points) - 1; ++$index) {
            $nextIndex = $index + 1;
            $width = $points[$nextIndex][0] - $points[$index][0];
            $controlOffset = $width / 3.0;
            $path .= ' C '.$this->format($points[$index][0] + $controlOffset).' '
                .$this->format($points[$index][1] + $tangents[$index] * $controlOffset).' '
                .$this->format($points[$nextIndex][0] - $controlOffset).' '
                .$this->format($points[$nextIndex][1] - $tangents[$nextIndex] * $controlOffset).' '
                .$this->format($points[$nextIndex][0]).' '.$this->format($points[$nextIndex][1]);
        }

        return $path;
    }

    public function gaugeArc(float $centerX, float $centerY, float $radius, float $ratio): string
    {
        $endAngle = M_PI + M_PI * $ratio;
        $endX = $centerX + $radius * cos($endAngle);
        $endY = $centerY + $radius * sin($endAngle);

        return 'M '.$this->format($centerX - $radius).' '.$this->format($centerY)
            .' A '.$this->format($radius).' '.$this->format($radius)
            .' 0 0 1 '.$this->format($endX).' '.$this->format($endY);
    }

    /**
     * @param non-empty-list<array{0: float, 1: float, 2: float}> $points
     *
     * @return non-empty-list<float>
     */
    private function monotoneTangents(array $points): array
    {
        if (1 === count($points)) {
            return [0.0];
        }

        $slopes = [];
        for ($index = 0; $index < count($points) - 1; ++$index) {
            $slopes[] = ($points[$index + 1][1] - $points[$index][1]) / ($points[$index + 1][0] - $points[$index][0]);
        }

        $tangents = [$slopes[0]];
        for ($index = 1; $index < count($points) - 1; ++$index) {
            $previous = $slopes[$index - 1];
            $next = $slopes[$index];
            $tangents[] = $previous * $next <= 0.0 ? 0.0 : 2.0 * $previous * $next / ($previous + $next);
        }
        $tangents[] = $slopes[count($slopes) - 1];

        return $tangents;
    }

    private function format(float $value): string
    {
        return rtrim(rtrim(number_format(round($value, 2), 2, '.', ''), '0'), '.');
    }
}
