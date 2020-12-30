<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * PhonesFilterValidation - This class check and valid query parameters necessary for filter feature for phones
 */
class PhonesFilterValidation
{    
        
    /**
     * Method validateBrand
     *
     * @param $brand 
     *
     * @return void
     */
    public function validateBrand($brand)
    {
        return (string)$brand;
    }
    
    /**
     * Method validateAvaibale
     *
     * @param $avaibale 
     *
     * @return void
     */
    public function validateAvaibale($avaibale)
    {
        if (in_array($avaibale, ["0", "1"])) {
            return $avaibale;
        } else {
            throw new \Exception('Veuillez entrez les bonnes valeurs : avaibale=1 pour les produits disponibles et avaibale=0 pour les produits non disponibles');
        } 
    }
    
    /**
     * Method validateMinprice
     *
     * @param $minprice
     *
     * @return void
     */
    public function validateMinprice($minprice)
    {
        if (!preg_match("#^[0-9]+$#", $minprice)) {
            throw new \Exception("La prix minimum doit être un entier positif");
        }
        if (preg_match("#^[0]+$#", $minprice)) {
            $minprice = 0;
        }
        return $minprice; 
    }
    
    /**
     * Method validateMaxprice
     *
     * @param $maxprice 
     *
     * @return void
     */
    public function validateMaxprice($maxprice)
    {
        if (!preg_match("#^[0-9]+$#", $maxprice)) {
            throw new \Exception("La prix maximum doit être un entier positif");
        }
        if (preg_match("#^[0]+$#", $maxprice)) {
            $maxprice = 0;
        }
        return (int)$maxprice;
    }
    
    /**
     * Method validIntervalPrice
     * This method check the interval enter $minprice and $maxprice
     *
     * @param $minprice $minprice 
     * @param $maxprice $maxprice 
     *
     * @return void
     */
    public function validIntervalPrice($minprice = null, $maxprice = null)
    {
        if (!$minprice) {
            $minprice = 0;
        }
        if (!$maxprice || in_array($maxprice, ["0", null])) {
            $maxprice = 1000000;
        }
        if ($maxprice < $minprice) {
            throw new \Exception("L'interval de prix renseigné n\est pas conforme : le prix maximum est inférieur au prix minimum");
        }
        return [$minprice, $maxprice];
    }
}