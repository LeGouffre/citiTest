<?php

namespace App\Repository;

use App\Entity\OldToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OldToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method OldToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method OldToken[]    findAll()
 * @method OldToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OldTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OldToken::class);
    }

    // /**
    //  * @return OldToken[] Returns an array of OldToken objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OldToken
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
