<?php

namespace App\Request;

use App\Request\ParamSortValidation;
use App\Request\PhonesFilterValidation;
use Symfony\Component\HttpFoundation\InputBag;

class ParamValidation
{    
    /**
     * phoneFilterValidation
     *
     * @var App\Request\PhonesFilterValidation
     */
    private $phoneFilterValidation;
    
    /**
     * paramSortValidation
     *
     * @var App\Request\ParamSortValidation
     */
    private $paramSortValidation;

    /**
     * brand
     *
     * @var string
     */
    private $brand;
    
    /**
     * avaibale
     *
     * @var int
     */
    private $avaibale;
    
    /**
     * minprice
     *
     * @var int
     */
    private $minprice;
    
    /**
     * maxprice
     *
     * @var int
     */
    private $maxprice; 
    
    /**
     * byPrice
     *
     * @var string
     */
    private $byprice;
    
    /**
     * limit
     *
     * @var int
     */
    private $limit;
    
    /**
     * offset
     *
     * @var int
     */
    private $offset;
    
    /**
     * search
     *
     * @var string
     */
    private $search;
    
    /**
     * perPage
     *
     * @var int
     */
    private $perPage;
    
    /**
     * page
     *
     * @var int
     */
    private $page;
    
    /**
     * byusername
     *
     * @var string
     */
    private $byusername;
    
    /**
     * byid
     *
     * @var string
     */
    private $byid;

    public function __construct(PhonesFilterValidation $phoneFilterValidation, ParamSortValidation $paramSortValidation)
    {
        $this->phoneFilterValidation = $phoneFilterValidation;
        $this->paramSortValidation = $paramSortValidation;
    }

    /**
     * Method validateQueryParam
     * This method check parameters in the request and call the good setter
     *
     * @param InputBag $queryParam [explicite description]
     *
     * @return void
     */
    public function validateParam(InputBag $queryParam)
    {
        foreach($queryParam->all() as $param => $value) {

            switch ($param) {
                case 'brand': 
                    $this->brand = $this->phoneFilterValidation->validateBrand($value);
                    break;
                case 'avaibale':
                    $this->avaibale = $this->phoneFilterValidation->validateAvaibale($value);
                    break;
                case 'minprice': 
                    $this->minprice = $this->phoneFilterValidation->validateMinprice($value);
                    break;
                case 'maxprice':
                    $this->maxprice = $this->phoneFilterValidation->validateMaxprice($value);
                    break; 
                case 'byprice':
                    $this->byprice = $this->paramSortValidation->validateByprice($value);
                    break;
                case 'limit':
                    $this->limit = $this->paramSortValidation->validateLimit($value);
                    break;
                case 'offset':
                    $this->offset = $this->paramSortValidation->validateOffset($value);
                    break;
                case 'search':
                    $this->search = $this->paramSortValidation->validateSearch($value);
                    break;
                case 'perpage':
                    $this->perPage = $this->paramSortValidation->validatePerPage($value);
                    break;
                case 'page':
                    $this->page = $this->paramSortValidation->validatePage($value);
                    break;
                case 'byusername':
                    $this->byusername = $this->paramSortValidation->validateByusername($value);
                    break;
                case 'byid':
                    $this->byid = $this->paramSortValidation->validateByid($value);
                    break;
                default : 
                    throw new \Exception("-" . $param . "- ne correspond à aucun paramètres disponible.");
            }
        }
        if (isset($this->minprice) || isset($this->maxprice)) {
    
            $interval = $this->phoneFilterValidation->validIntervalPrice($this->minprice, $this->maxprice);
            $this->minprice = $interval[0];
            $this->maxprice = $interval[1];
        }
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function getAvaibale()
    {
        return $this->avaibale;
    }

    public function getMinprice()
    {
        return $this->minprice;
    }

    public function getMaxprice()
    {
        return $this->maxprice;
    }

    public function getByprice()
    {
        return $this->byprice;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getSearch()
    {
        return $this->search;
    }

    public function getPerPage()
    {
        return $this->perPage;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getByusername()
    {
        return $this->byusername;
    }

    public function getByid()
    {
        return $this->byid;
    }
}