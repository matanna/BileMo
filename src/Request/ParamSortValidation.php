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
     * @return int
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
     * @return int
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
     * @return string
     */
    public function validateSearch($search)
    {
        if (!preg_match("#^[0-9a-zA-Z]+$#", $search)) {
            throw new \Exception("Les caractères spéciaux ne sont pas autorisés dans la fonction search.");
        }
        return $search;
    }
    
    /**
     * Method validatePerPage
     *
     * @param $perPage
     *
     * @return int
     */
    public function validatePerPage($perPage)
    {
        if (!preg_match("#^[0-9]+$#", $perPage)) {
            throw new \Exception("Le paramètre perpage doit être un entier positif.");
        }
        return $perPage;
    }
    
    /**
     * Method validatePage
     *
     * @param $page $page [explicite description]
     *
     * @return int
     */
    public function validatePage($page)
    {
        if (!preg_match("#^[0-9]+$#", $page)) {
            throw new \Exception("Le paramètre page doit être un entier positif.");
        }
        return $page;
    }

    public function validateByusername($byusername)
    {
        if (!in_array($byusername, ['ASC', 'DESC'])) {
            throw new \Exception("$byusername n'est pas acceptable. Veuillez renseigner ASC pour le tri en ordre alphabétique ou DESC pour l'inverse.");
        }
        return $byusername;
    }

    public function validateByid($byid)
    {
        if (!in_array($byid, ['ASC', 'DESC'])) {
            throw new \Exception("$byid n'est pas acceptable. Veuillez renseigner ASC pour le tri en ordre croissant ou DESC pour l'inverse.");
        }
        return $byid;
    }
}
