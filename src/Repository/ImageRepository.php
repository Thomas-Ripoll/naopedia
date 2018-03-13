<?php

namespace App\Repository;

use App\Entity\Image;
use App\Entity\Bird;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ImageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Image::class);
    }

    
    public function findByTrend(Bird $bird)
    {
        return $this->createQueryBuilder('i')
            ->where('i.bird = :value')->setParameter('value', $bird->getId())
            ->setMaxResults(1)//On veux seulement le plus grand 
            ->orderBy('i.likesNumber', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
}
