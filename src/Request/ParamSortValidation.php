<?php

namespace App\Request;

/**
 * ParamSortValidation - This class check and valid query parameters necessary for sort results in the response
 */
class ParamSortValidation
{    
    /**
     * Method validateByPrice
     *
     * @param $byprice
     *
     * @return string
     */
    public function validateByprice($byprice)
    {
        if (!in_array($byprice, ['ASC', 'DESC'])) {
            throw new \Exception("$byprice n'est pas acceptable. Veuillez renseigner ASC pour le tri en mode croissant ou DESC pour le mode décroissant");
        }
        return $byprice;
    }
    
    /**
     * Method validateOffset
     *
     * @param $offset
     *
     * @return void
     */
    public function validateOffset($offset){
        if (!preg_match("#^[0-9]+$#", $offset)) {
            throw new \Exception("Le parametre offset doit être un entier positif");
        }
        return $offset;
    }
    
    /**
     * Method validateLimit
     *
     * @param $limit 
     * @return void
     */
    public function validateLimit($limit){
        if (!preg_match("#^[0-9]+$#", $limit)) {
            throw new \Exception("Le parametre limit doit être un entier positif");
        }
        return $limit;
    }
    
    /**
     * Method validateSearch
     *
     * @param $search
     *
     * @return void
     */
    public function validateSearch($search)
    {
        if (!preg_match("#^[0-9a-zA-Z]+$#", $search)) {
            throw new \Exception("Les caractères spéciaux ne sont pas autorisés dans la fonction search.");
        }
        return $search;
    }
}
