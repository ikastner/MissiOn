<?php

namespace App\Repository;

use App\Entity\Missions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Missions>
 */
class MissionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Missions::class);
    }

    //    /**
    //     * @return Missions[] Returns an array of Missions objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    public function findByFilters(array $filters)
    {
        $queryb = $this->createQueryBuilder('m');

        if (!empty($filters['tjm'])) {
            $queryb->andWhere('m.tjm <= :tjm')
               ->setParameter('tjm', $filters['tjm']);
        }

        if (!empty($filters['niveau'])) {
            $queryb->andWhere('m.niveau = :niveau')
               ->setParameter('niveau', $filters['niveau']);
        }

        if (!empty($filters['search'])) {
            $queryb->andWhere('m.title LIKE :search OR m.description LIKE :search')
               ->setParameter('search', '%' . $filters['search'] . '%');
        }

        return $queryb->getQuery()->getResult();
    }
}
