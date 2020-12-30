<?php

namespace App\Repository;

use App\Entity\Phone;
use App\Request\ParamValidation;
use App\Request\PhonesFilterValidation;
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
    private ParamValidation $paramValidation;

    public function __construct(ManagerRegistry $registry, ParamValidation $paramValidation)
    {
        parent::__construct($registry, Phone::class);
        
        $this->paramValidation = $paramValidation;
    }

    /**
     * @param $request Request 
     * 
     * @return Phone[] Returns an array of Phone objects
     */
    public function filter(Request $request)
    {
        $this->paramValidation->validateParam($request->query);
        
        if ($this->paramValidation->getBrand()) {

            return $this->createQueryBuilder('phone')
                   ->leftJoin('phone.brand', 'phoneBrand')
                   ->andWhere('phoneBrand.brand = :brand')
                   ->setParameter('brand', $this->paramValidation->getBrand())
                   ->getQuery()
                   ->execute()
            ;
        }
        
        if (in_array($this->paramValidation->getAvaibale(), ["0", "1"])) {
            
            return $this->createQueryBuilder('phone')
                   ->andWhere('phone.availability = :availability')
                   ->setParameter('availability', $this->paramValidation->getAvaibale())
                   ->getQuery()
                   ->execute()
            ;
        }
        
        if ($this->paramValidation->getMinprice() || $this->paramValidation->getMaxprice()) {
            
            return $this->createQueryBuilder('phone')
                   ->andWhere('phone.price > :minprice')
                   ->andWhere('phone.price < :maxprice')
                   ->setParameter('minprice', $this->paramValidation->getMinprice())
                   ->setParameter('maxprice', $this->paramValidation->getMaxprice())
                   ->getQuery()
                   ->execute()
            ;
        }
        
    }
}
