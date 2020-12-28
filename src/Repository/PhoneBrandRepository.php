<?php

namespace App\Repository;

use App\Entity\PhoneBrand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PhoneBrand|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhoneBrand|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhoneBrand[]    findAll()
 * @method PhoneBrand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhoneBrandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhoneBrand::class);
    }

    // /**
    //  * @return PhoneBrand[] Returns an array of PhoneBrand objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PhoneBrand
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
