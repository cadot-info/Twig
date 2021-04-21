<?php

namespace App\Twig;

use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class GetlogoExtension extends AbstractExtension
{
    protected $packages;

    public function __construct(Packages $packages)
    {
        $this->packages = $packages;
    }



    public function getFunctions(): array
    {
        return [
            new TwigFunction('getlogo', [$this, 'getlogo']),
        ];
    }

    public function getlogo()
    {
        return $this->packages->getUrl('build/images/' . $_ENV['SITE_LOGO']);
    }
}
