<?php

namespace App\Repository;

use App\Entity\EquipeTournoi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EquipeTournoi>
 *
 * @method EquipeTournoi|null find($id, $lockMode = null, $lockVersion = null)
 * @method EquipeTournoi|null findOneBy(array $criteria, array $orderBy = null)
 * @method EquipeTournoi[]    findAll()
 * @method EquipeTournoi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipeTournoiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipeTournoi::class);
    }

//    /**
//     * @return EquipeTournoi[] Returns an array of EquipeTournoi objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EquipeTournoi
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
