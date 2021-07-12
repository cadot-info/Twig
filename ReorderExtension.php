<?php

namespace App\CMTwig;

use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Doctrine\ORM\EntityManagerInterface;

class ReorderExtension extends AbstractExtension
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('reorder', [$this, 'reorder']),
        ];
    }

    public function reorder($repository)
    {
        $array = [];
        $objet = $this->em->getRepository("App:" . ucfirst($repository))->findall();
        if ($base = $this->em->getRepository("App:Sortable")->findOneBy(['entite' => $repository])) {
            $sortable = explode(',', $base->getordre()); //tableau des ordres
            foreach ($sortable as $index => $num) {
                $res =  array_filter(
                    $objet,
                    function ($e) use (&$num) {
                        return $e->getId() == $num;
                    }
                );
                $array[$index] = reset($res);
            }
            return $array;
        } else return $objet;
    }
}
