<?php

namespace App\CMTwig;

use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Doctrine\ORM\EntityManagerInterface;

class HasLevelExtension extends AbstractExtension
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function getFunctions(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFunction('haslevel', [$this, 'haslevel']),
        ];
    }


    public function haslevel($id)
    {
        $user = $this->em->getRepository("App:User");
        return $user->find($id)->getRoles()[0];
    }
}
