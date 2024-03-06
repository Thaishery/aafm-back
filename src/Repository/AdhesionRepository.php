<?php

namespace App\Repository;

use App\Entity\Adhesion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Adhesion>
 *
 * @method Adhesion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adhesion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adhesion[]    findAll()
 * @method Adhesion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdhesionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Adhesion::class);
    }

//    /**
//     * @return Adhesion[] Returns an array of Adhesion objects
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

//    public function findOneBySomeField($value): ?Adhesion
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
