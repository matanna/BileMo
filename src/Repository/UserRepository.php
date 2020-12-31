<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Client;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

        
    /**
     * Method findUsersByClient
     *
     * @param Client $client 
     * @param Request $request
     *
     * @return void
     */
    public function findUsersByClient(Client $client, Request $request)
    {
        if($request->query) {

            $this->paramValidation->validateParam($request->query);
            
            /*
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
            */
            $results = $this->pagination->paginate($query->execute(), $this->perPage, $this->page); 

            return $results;    
        } 
    }
}
