<?php

namespace App\Response;

class Pagination
{        
    /**
     * results
     *
     * @var []
     */
    private $results;
    
    /**
     * nbrPerPage
     *
     * @var int
     */
    private $nbrPerPage;
    
    /**
     * numPage
     *
     * @var int
     */
    private $numPage;
    
    /**
     * nbPage
     *
     * @var int
     */
    private $nbPage;

    /**
     * Method paginate
     *
     * @param $results 
     * @param $nbrPerPage 
     * @param $numPage $numPage 
     *
     * @return void
     */
    public function paginate($results, $nbrPerPage, $numPage)
    {
        $this->results = $results;
        $this->nbrPerPage = $nbrPerPage;
        $this->numPage = $numPage;

        $this->nbPage = $this->countNbrPage();

        $firstPageResult = $this->findFirstResult();

        return $this->createPage($firstPageResult);  
    }
    
    /**
     * Method countPage
     *
     * @param $results 
     * @param $nbrPerPage 
     *
     * @return void
     */
    private function countNbrPage()
    {
        return (int)ceil(count($this->results) / $this->nbrPerPage);
    }
    
    /**
     * Method findFirstResult
     *
     * @param $numPage
     * @param $nbrPerPage
     *
     * @return void
     */
    private function findFirstResult()
    {
        return $this->numPage * $this->nbrPerPage;
    }
    
    /**
     * Method createPage
     *
     * @return []
     */
    private function createPage($firstPageResult)
    {
        $currentPage = [];

        $lastPageResult = $firstPageResult + $this->nbrPerPage;

        for ($i = $firstPageResult; $i < $lastPageResult; $i++) {
            $currentPage[$i] = $this->results[$i];
        }
        return $currentPage;
    }
}