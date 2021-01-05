<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Client;
use App\Response\Pagination;
use App\Request\ParamValidation;
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
    
    /**
     * paramValidation
     *
     * @var mixed
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
     * @var mixed
     */
    private $pagination;

    public function __construct(ManagerRegistry $registry, ParamValidation $paramValidation,
        Pagination $pagination
    ) {
        parent::__construct($registry, User::class);

        $this->paramValidation = $paramValidation;
        $this->pagination = $pagination;
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
            
            $qb = $this->createQueryBuilder('user')
                       ->leftJoin('user.client', 'userClient')
                       ->where('userClient = :client')
                       ->setParameter('client', $client);

            if ($this->paramValidation->getByusername()) {
                $qb->orderBy('user.username', $this->paramValidation->getByusername());
            }
            
            if ($this->paramValidation->getByid()) {
                $qb->orderBy('user.id', $this->paramValidation->getByid()); 
            }

            if ($this->paramValidation->getSearch()) {
                $qb->andWhere('user.username LIKE :username')
                   ->setParameter('username', '%'.$this->paramValidation->getSearch().'%');
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
}
