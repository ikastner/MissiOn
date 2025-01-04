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
        $queryb = $this->createQueryBuilder('f')
            ->leftJoin('f.competences', 'c') // Jointure avec la table des compétences
            ->addSelect('c'); // Sélection des compétences pour les conditions

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

        // Filtrage par compétences sélectionnées
        if (!empty($filters['competences'])) {
            // tableau contenant les compétences sélectionnées
            $competenceNames = $filters['competences'];

            // filtre des freelances qui possèdent ces compétences
            $queryb->andWhere('c.name IN (:competences)')
                   ->setParameter('competences', $competenceNames);
        }

        return $queryb->getQuery()->getResult();
    }

   
}
