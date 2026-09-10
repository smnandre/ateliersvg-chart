<?php

declare(strict_types=1);

namespace Atelier\Chart\Renderer\Svg;

use Atelier\Chart\Accessibility\DataSummary;
use Atelier\Chart\Model\ChartModel;
use Atelier\Chart\Theme\Theme;
use Atelier\Svg\Document;
use Atelier\Svg\Element\Builder;
use Atelier\Svg\Element\Descriptive\DescElement;
use Atelier\Svg\Element\Descriptive\TitleElement;
use Atelier\Svg\Element\Structural\GroupElement;

final class SvgDocumentFactory
{
    public function create(ChartModel $chart, Theme $theme, SvgRenderOptions $options = new SvgRenderOptions()): Builder
    {
        $builder = new Builder();
        $builder->svg(round($chart->width(), 2), round($chart->height(), 2));
        $builder->attr('viewBox', sprintf('0 0 %s %s', $this->format($chart->width()), $this->format($chart->height())));
        $builder->attr('font-family', $theme->fontFamily);
        if ($options->classes) {
            $builder->attr('class', 'atelier-chart');
        }
        if ($options->dataAttributes) {
            $builder->attr('data-renderer', 'atelier/chart');
        }

        $root = $builder->getSvg();
        $root->setAttribute('role', 'img');
        $root->setAttribute('aria-label', $chart->title());

        $title = new TitleElement();
        $title->setContent($chart->title());
        $root->appendChild($title);

        $description = new DescElement();
        $description->setContent(implode(' ', array_filter([$chart->description(), DataSummary::for($chart)])));
        $root->appendChild($description);

        $builder->rect(0.0, 0.0, round($chart->width(), 2), round($chart->height(), 2), 0.0);
        $builder->attr('fill', $theme->backgroundColor);
        if ($options->classes) {
            $builder->attr('class', 'atelier-chart__background');
        }
        $builder->end();

        return $builder;
    }

    /** Group adjacent labels with identical presentation attributes without changing paint order. */
    public function finish(Builder $builder): Document
    {
        $root = $builder->getSvg();
        $children = $root->getChildren();
        $root->clearChildren();
        $styleNames = array_flip(['fill', 'font-size', 'font-weight', 'text-anchor']);

        for ($index = 0, $count = count($children); $index < $count; ++$index) {
            $first = $children[$index];
            $style = array_intersect_key($first->getAttributes(), $styleNames);
            $end = $index + 1;
            if ('text' === $first->getTagName()) {
                while ($end < $count && 'text' === $children[$end]->getTagName()
                    && $style === array_intersect_key($children[$end]->getAttributes(), $styleNames)) {
                    ++$end;
                }
            }
            if ($end === $index + 1 || [] === $style) {
                $root->appendChild($first);
                continue;
            }

            $group = new GroupElement();
            foreach ($style as $name => $value) {
                $group->setAttribute($name, $value);
            }
            for (; $index < $end; ++$index) {
                $child = $children[$index];
                foreach ($style as $name => $value) {
                    $child->removeAttribute($name);
                }
                $group->appendChild($child);
            }
            --$index;
            $root->appendChild($group);
        }

        return $builder->getDocument();
    }

    private function format(float $value): string
    {
        return rtrim(rtrim(number_format(round($value, 2), 2, '.', ''), '0'), '.');
    }
}
