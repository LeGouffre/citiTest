<?php

namespace App\Repository;

use App\Entity\TokenNumber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TokenNumber|null find($id, $lockMode = null, $lockVersion = null)
 * @method TokenNumber|null findOneBy(array $criteria, array $orderBy = null)
 * @method TokenNumber[]    findAll()
 * @method TokenNumber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenNumberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenNumber::class);
    }
    /**
     * @return TokenNumber[]
     * array of token number UpdateAt before date
     */

    public function getTokenByUpdate($date)
    {
        return $this->createQueryBuilder('t')
        ->andWhere('t.UpdateAt <= :date')
        ->setParameter("date", $date)
        ->getQuery()->getResult();
    }
    // /**
    //  * @return TokenNumber[] Returns an array of TokenNumber objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TokenNumber
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
