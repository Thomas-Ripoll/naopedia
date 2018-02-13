<?php

namespace App\Repository;

use App\Entity\Bird;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BirdRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Bird::class);
    }
    
    public function search($query){
        return $this->createQueryBuilder('b')
            ->where('b.name LIKE :value OR b.latinName LIKE :value')
                ->setParameter('value', '%'.$query."%")
                ->getQuery()
                ->getResult();
    }
    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('b')
            ->where('b.something = :value')->setParameter('value', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
