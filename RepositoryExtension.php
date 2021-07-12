<?php

namespace App\CMTwig;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class RepositoryExtension extends AbstractExtension
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('findall', [$this, 'findall']),
            new TwigFunction('find', [$this, 'find']),
            new TwigFunction('findOneBy', [$this, 'findOneBy']),
            new TwigFunction('findBy', [$this, 'findBy']),
        ];
    }

    /**
     * Method findall
     *
     * @param string $repository 
     *
     * @return array of entities
     */
    public function findall(string $repository)
    {
        return $this->em->getRepository("App:" . ucfirst($repository))->findall();
    }
    /**
     * Method find
     *
     * @param string $repository 
     * @param int $id 
     *
     * @return array of entities
     */
    public function find(string $repository, int $id)
    {
        return $this->em->getRepository("App:" . ucfirst($repository))->find($id);
    }

    /**
     * Method findOneBy
     *
     * @param string $repository 
     * @param array $criteria 
     * @param array $orderBy 
     *
     * @return entitie
     */
    public function findOneBy(string $repository, array $criteria, array $orderBy = null)
    {
        return $this->em->getRepository("App:" . ucfirst($repository))->findOneBy($criteria,  $orderBy = null);
    }
    /**
     * Method findBy
     *
     * @param string $repository 
     * @param array $criteria 
     * @param array $orderBy 
     * @param int $limit  
     * @param int $offset 
     *
     * @return array of entities
     */
    public function findBy(string $repository, array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->em->getRepository("App:" . ucfirst($repository))->findBy($criteria,  $orderBy = null, $limit = null, $offset = null);
    }
}
