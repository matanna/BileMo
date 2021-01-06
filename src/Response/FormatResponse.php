<?php

namespace App\Response;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * This class format data for display a good response in json
 */
class FormatResponse
{
    private $client;
    
    public function __construct(TokenStorageInterface $token)
    {
        $this->client = $token->getToken()->getUser();
    }

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
        
        $response['current_client'] = $this->formatCurrentClient();

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

    /**
     * Method formatCurrentClient
     *
     * @return array
     */
    private function formatCurrentClient()
    {
        $currentClient = [];

        $currentClient['username'] = $this->client->getUsername();
        $currentClient['fullname'] = $this->client->getFullname();
        $currentClient['email'] = $this->client->getEmail();

        return $currentClient;
    }
}