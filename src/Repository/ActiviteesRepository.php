<?php

namespace App\Repository;

use App\Entity\Activitees;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activitees>
 *
 * @method Activitees|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activitees|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activitees[]    findAll()
 * @method Activitees[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActiviteesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activitees::class);
    }

//    /**
//     * @return Activitees[] Returns an array of Activitees objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Activitees
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
