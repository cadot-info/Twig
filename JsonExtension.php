<?php

namespace App\CMTwig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class JsonExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('JsonPretty', [$this, 'jsonpretty', ['is_safe' => ['html']]]),
        ];
    }



    public function jsonpretty($json)
    {
        foreach (json_decode($json) as $key => $value) {
            $td = [];
            // if (\is_object($value)) $value = (array)$value;
            foreach ($value as $k => $v) {
                $td[] = "<b>$k</b>: $v";
            }
            $tr[] = \implode(',', $td);
        }

        return implode('<br>', $tr);
    }
}
