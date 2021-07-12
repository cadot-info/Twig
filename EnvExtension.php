<?php

namespace App\CMTwig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class EnvExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getenv', [$this, 'getenv']),
        ];
    }

    /**
     * Method getenv for get env superglobal variable
     *
     * @param string $var [variable]
     *
     * @return void
     */
    public function getenv(string $var)
    {
        return $_ENV[$var];
    }
}
