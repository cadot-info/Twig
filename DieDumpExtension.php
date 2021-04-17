<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class DieDumpExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('dd', [$this, 'dd']),
        ];
    }

    public function dd($value)
    {
        dd($value);
    }
}
