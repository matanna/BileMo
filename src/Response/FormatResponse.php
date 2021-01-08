<?php

namespace App\Response;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * This class format data for display a good response in json
 */
class FormatResponse
{    
    /**
     * Method format
     *
     * @param $data 
     * @param $pages
     *
     * @return void
     */
    public function format(Array $data, $pages = null)
    {
        $response['data'] = $this->formatData($data);

        if ($pages != null) {
            $response['pages'] = $pages;
        }

        return $response;
    }
       
    /**
     * Method formatData
     *
     * @param $data 
     *
     * @return void
     */
    private function formatData($data)
    {
        $newData = [];

        foreach($data as $key => $element) {
            if (is_int($key)) {
                $newData['item n°' . $key] = $element;
                
            } else {
                $newData[$key] = $element; 
            }
        }
        return $newData;
    }
}