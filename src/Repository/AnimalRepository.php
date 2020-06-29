<?php

namespace App\Repository;

use App\Entity\Animal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AnimalRepository extends ServiceEntityRepository{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Animal::class);
    }

    public function porColor($color){
        $qb = $this->createQueryBuilder('a')
                                    ->andWhere("a.color = :color")
                                    ->setParameter('color', $color)   
                                    ->orderBy('a.id', 'DESC')                                      
                                    ->getQuery();
        $resultSet = $qb->execute();

            $coleccion = array(
                'name' => 'Coleccion de animales',
                'animales' => $resultSet,
            );

        return $coleccion;
    }
}


