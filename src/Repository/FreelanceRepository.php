<?php

namespace App\Repository;

use App\Entity\Freelance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Freelance>
 */
class FreelanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Freelance::class);
    }

    public function findByFilters(array $filters)
    {
        $queryb = $this->createQueryBuilder('f');

        if (!empty($filters['TJM'])) {
            $queryb->andWhere('f.TJM <= :TJM')
               ->setParameter('TJM', $filters['TJM']);
        }

        if (!empty($filters['pays'])) {
            $queryb->andWhere('f.pays = :pays')
               ->setParameter('pays', $filters['pays']);
        }

        if (!empty($filters['ville'])) {
            $queryb->andWhere('f.ville = :ville')
               ->setParameter('ville', $filters['ville']);
        }

        if (!empty($filters['search'])) {
            $queryb->andWhere('f.nom LIKE :search OR f.titre LIKE :search')
               ->setParameter('search', '%' . $filters['search'] . '%');
        }

        return $queryb->getQuery()->getResult();
    }

   
}
