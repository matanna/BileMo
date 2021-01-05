<?php

namespace App\Response;

use App\Response\FormatResponse;

class Pagination
{    
    /**
     * formatResponse
     *
     * @var App\Response\FormatResponse
     */
    private $formatResponse;
    
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

    public function __construct(FormatResponse $formatResponse)
    {
        $this->formatResponse= $formatResponse;
    }

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
        if ($results == []) {
            return null;
        }
        
        $this->results = $results;
        $this->nbrPerPage = $nbrPerPage;
        $this->numPage = $numPage - 1;

        $this->nbPage = $this->countNbrPage();

        if ($numPage > $this->nbPage) {
            throw new \Exception ("Le numéro de la page demandée est supérieur aux nombre de pages disponibles pour cette requête.");
        }

        $firstPageResult = $this->findFirstResult();

        $currentPage =  $this->createPage($firstPageResult);

        $meta = $this->createMetadata($currentPage);

        $response = $this->formatResponse->format($currentPage, $meta);

        return $response;
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
        
        if ($lastPageResult > count($this->results)) {
            $lastPageResult = count($this->results);
        }

        for ($i = $firstPageResult; $i < $lastPageResult; $i++) {
            $currentPage[$i] = $this->results[$i];
        }
        return $currentPage;
    }
    
    /**
     * Method createMetadata
     *
     * @param $currentPage 
     *
     * @return void
     */
    private function createMetadata($currentPage)
    {
        $meta = [];

        $meta['results_per_page'] = $this->nbrPerPage;
        $meta['num_current_page'] = $this->numPage + 1;
        $meta['num_total_pages'] = $this->nbPage;
        $meta['results_in_current_page'] = count($currentPage);

        return $meta;

    }
}