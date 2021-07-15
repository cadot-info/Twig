<?php

namespace App\CMTwig;

use Twig\TwigFilter;
use App\CMService\FileFunctions;
use Twig\Extension\AbstractExtension;

class StringExtension extends AbstractExtension
{
    protected $FileFunctions;

    public function __construct(FileFunctions $FileFunctions)
    {
        $this->FileFunctions = $FileFunctions;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('sanitize', [$this, 'sanitize']),
        ];
    }


    public function sanitize($value)
    {
        return $this->FileFunctions->sanitize($value);
    }
}
