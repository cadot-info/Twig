<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;


class ObjetPropertiesExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('objetProperties', [$this, 'objetProperties']),
        ];
    }


    public function objetProperties($objets)
    {
        $response = [];
        if (is_array($objets)) $objets = $objets[0];
        foreach ((array)$objets as $key => $value) {
            $string = preg_replace('/[\x00]/u', '\\', $key);
            $clef = substr($string, strrpos($string, '\\') + 1);
            $response[] = $clef;
        }
        return $response;
    }
}
