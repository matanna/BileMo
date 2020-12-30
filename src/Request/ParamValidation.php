<?php

namespace App\Request;

use App\Request\PhonesFilterValidation;
use Symfony\Component\HttpFoundation\InputBag;

class ParamValidation
{    
    /**
     * phoneFilterValidation
     *
     * @var mixed
     */
    private $phoneFilterValidation;

    /**
     * brand
     *
     * @var mixed
     */
    private $brand;
    
    /**
     * avaibale
     *
     * @var mixed
     */
    private $avaibale;
    
    /**
     * minprice
     *
     * @var mixed
     */
    private $minprice;
    
    /**
     * maxprice
     *
     * @var mixed
     */
    private $maxprice; 

    public function __construct(PhonesFilterValidation $phoneFilterValidation)
    {
        $this->phoneFilterValidation = $phoneFilterValidation;
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
}