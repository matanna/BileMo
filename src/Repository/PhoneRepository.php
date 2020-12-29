<?php

namespace App\Repository;

use App\Entity\Phone;
use App\Request\QueryValidation;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Phone|null find($id, $lockMode = null, $lockVersion = null)
 * @method Phone|null findOneBy(array $criteria, array $orderBy = null)
 * @method Phone[]    findAll()
 * @method Phone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Phone::class);
    }

    /**
     * @param $request Request 
     * 
     * @return Phone[] Returns an array of Phone objects
     */
    public function filter(Request $request)
    {
        $queryValidation = new QueryValidation();
        $queryValidation->validateQueryParam($request->query);
        
        if ($queryValidation->getBrand()) {

            return $this->createQueryBuilder('phone')
                   ->leftJoin('phone.brand', 'phoneBrand')
                   ->andWhere('phoneBrand.brand = :brand')
                   ->setParameter('brand', $queryValidation->getBrand())
                   ->getQuery()
                   ->execute()
            ;
        }
        
        if (in_array($queryValidation->getAvaibale(), ["0", "1"])) {
            
            return $this->createQueryBuilder('phone')
                   ->andWhere('phone.availability = :availability')
                   ->setParameter('availability', $queryValidation->getAvaibale())
                   ->getQuery()
                   ->execute()
            ;
        }
        
        if ($queryValidation->getMinprice() || $queryValidation->getMaxprice()) {
            
            return $this->createQueryBuilder('phone')
                   ->andWhere('phone.price > :minprice')
                   ->andWhere('phone.price < :maxprice')
                   ->setParameter('minprice', $queryValidation->getMinprice())
                   ->setParameter('maxprice', $queryValidation->getMaxprice())
                   ->getQuery()
                   ->execute()
            ;
        }
        
    }
}
