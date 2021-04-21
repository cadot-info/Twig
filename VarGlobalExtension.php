<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class VarGlobalExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [$this, 'doSomething']),
        ];
    }

    public function doSomething($value)
    {
        return
            $this->getApplication()->find('demo:greet');
    }
}
