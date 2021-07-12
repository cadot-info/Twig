<?php

namespace App\CMTwig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TemplateExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('formrow', [$this, 'formrow'], ['is_safe' => ['html']]),
            new TwigFunction('formcol', [$this, 'formcol'], ['is_safe' => ['html']]),
            new TwigFunction('formend', [$this, 'formend'], ['is_safe' => ['html']]),
        ];
    }

    public function formrow()
    {
        return ('<div class="row">');
    }
    public function formend()
    {
        return ('</div>');
    }

    public function formcol($value)
    {
        return '<div class="col-' . $value . '">';
    }
}
