<?php

namespace App\CMTwig;

use App\CMService\EntityFunctions;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Doctrine\ORM\EntityManagerInterface;

class ReorderExtension extends AbstractExtension
{
    protected $em, $fe;

    public function __construct(EntityManagerInterface $em, EntityFunctions $fe)
    {
        $this->em = $em;
        $this->fe = $fe;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('reorder', [$this, 'reorder']),
        ];
    }

    public function reorder($repository, $donnees = '')
    {
        return $this->fe->reorder($repository, $donnees);
    }
}
