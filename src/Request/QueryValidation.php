<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\JsonResponse;

class QueryValidation
{
    private $brand;

    private $avaibale;

    private $minprice;

    private $maxprice;

    /*public  function __construct(Request $request)
    {
        $this->brand = $request->query->get('brand');
        $this->avaibale = $request->query->get('avaibale');
        $this->minprice = $request->query->get('minprice');
        $this->maxprice = $request->query->get('maxprice');
    }*/

    public function validateQueryParam(InputBag $queryParam)
    {
        foreach($queryParam->all() as $param => $value) {
            
            switch ($param) {
                case 'brand': 
                    $this->setBrand($value);
                    
                case 'avaibale':
                    $this->setAvaibale($value);
                    
                case 'minprice': 
                    $this->setMinprice($value);
                    
                case 'maxprice':
                    $this->setMaxprice($value);
                    
            }
        }
          
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    public function getAvaibale()
    {
        return $this->avaibale;
    }

    public function setAvaibale($avaibale)
    {
        
        if (in_array($avaibale, ["0", "1"])) {
            
            $this->avaibale = $avaibale;
        } else {
            throw new \Exception('Veuillez entrez les bonnes valeurs : avaibale=1 pour les produits disponibles et avaibale=0 pour les produits non disponibles');
        } 
    }

    public function getMinprice()
    {
        return $this->minprice;
    }

    public function setMinprice($minprice)
    {
        
        if (!$minprice) {
            $this->minprice = 0;
            
        } else {
            $this->minprice = $minprice;
            
        }
    }

    public function getMaxprice()
    {
        return $this->maxprice;
    }

    public function setMaxprice($maxprice)
    {
        if (!$maxprice || in_array($maxprice, ["0", null])) {
            $this->maxprice = 1000000;
        } else {
            $this->maxprice = $maxprice;
        }
        $this->validIntervalPrice();
    }

    public function validIntervalPrice()
    {
        if ($this->maxprice < $this->minprice) {
            
            throw new \Exception("L'interval de prix renseigné n\est pas conforme : le prix maximum est inférieur au prix minimum");
        }
    }
}