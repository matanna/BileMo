<?php

namespace App\Repository;

use App\Entity\Phone;
use App\Response\Pagination;
use App\Request\ParamValidation;
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
    /**
     * paramValidation
     *
     * @var App\Request\ParamValidation
     */
    private $paramValidation;
    
    /**
     * perPage
     *
     * @var int
     */
    private $perPage = 10;
    
    /**
     * page
     *
     * @var int
     */
    private $page = 1;
    
    /**
     * pagination
     *
     * @var App\Response\Pagination
     */
    private $pagination;

    public function __construct(ManagerRegistry $registry, 
        ParamValidation $paramValidation, Pagination $pagination
    ) {
        parent::__construct($registry, Phone::class);

        $this->paramValidation = $paramValidation;
        $this->pagination = $pagination;
    }

    /**
    * @param $request Request 
    * 
    * @return Phone[] Returns an array of Phone objects
    */
    public function findPhones(Request $request)
    {
        if($request->query) {

            $this->paramValidation->validateParam($request->query);
            
        
            $qb = $this->createQueryBuilder('phone')
                       ->leftJoin('phone.brand', 'phoneBrand')
                       ->orderBy('phone.price', $this->paramValidation->getByprice()); 

            if ($this->paramValidation->getBrand()) {
                $qb->andWhere('phoneBrand.brand = :brand')
                   ->setParameter('brand', $this->paramValidation->getBrand());
            }

            if (in_array($this->paramValidation->getAvaibale(), ["0", "1"])) {
                $qb->andWhere('phone.availability = :availability')
                   ->setParameter('availability', $this->paramValidation->getAvaibale());
            }

            if ($this->paramValidation->getMinprice()) {
                $qb->andWhere('phone.price > :minprice')
                   ->setParameter('minprice', $this->paramValidation->getMinprice());
            }

            if ($this->paramValidation->getMaxprice()) {
                $qb->andWhere('phone.price < :maxprice')
                ->setParameter('maxprice', $this->paramValidation->getMaxprice());
            }

            if ($this->paramValidation->getSearch()) {
                $qb->andWhere('phone.model LIKE :model')
                   ->setParameter('model', '%'.$this->paramValidation->getSearch().'%');
            }
                
            $query = $qb->getQuery();

            if ($this->paramValidation->getPerPage()) {
                $this->perPage = $this->paramValidation->getPerPage();
            }

            if ($this->paramValidation->getPage()) {
                $this->page = $this->paramValidation->getPage();
            }
            
            $results = $this->pagination->paginate($query->execute(), $this->perPage, $this->page); 

            return $results;    
        }
    }

    public function findLastId()
    {
        $qb = $this->createQueryBuilder('p')
                   ->setMaxResults(1)
                   ->orderBy('p.id', 'DESC')
                   ->getQuery()
                   ->execute();
        return $qb[0];
    }
}
