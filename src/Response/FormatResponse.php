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
    const VERB = [
        'self' => "GET", 
        'replace' => "PUT", 
        'modify' => "PATCH", 
        'delete' => "DELETE"
    ];

    const ROUTE_PREFIX = [
        'GET' => 'show',
        'PUT' => 'update',
        'PATCH' => 'update',
        'DELETE' => 'delete'
    ];

    /**
     * client
     *
     * @var Client
     */
    private $client;
    
    /**
     * uri
     *
     * @var string
     */
    private $uri;
    
    /**
     * currentRoute
     *
     * @var string
     */
    private $currentRoute;
    
    /**
     * router
     *
     * @var RouterInterface
     */
    private $router;
    
    public function __construct(TokenStorageInterface $token, 
        RequestStack $requestStack, RouterInterface $router
    ) {
        $this->client = $token->getToken()->getUser();
        $this->uri = $requestStack->getCurrentRequest()->getUri();
        $this->currentRoute = $requestStack->getCurrentRequest()->get('_route');
        $this->router = $router;
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
        
        $links = $this->formatLinks();
        if ($links != null) {
            $response['_links'] = $links;
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
    
    /**
     * Method formatLinks
     *
     * @return array
     */
    private function formatLinks()
    {
        //We get resource name by the route
        $resource = $this->addRoutePrefix($this->currentRoute);
        
        if ((explode('_',$this->currentRoute))[0] == 'list') {
            return;
        }
        
        $links = [];

        $uri = (explode('?', $this->uri))[0];

        //We test all links with all possibility and we return valid links
        foreach (self::VERB as $key => $element) {
            $route = self::ROUTE_PREFIX[$element] . '_' . $resource;

            if ($this->router->getRouteCollection()->get($route) != null) {
                $links[$key] = [
                    "Href" => $uri,
                    "Rel" => $route,
                    "Method" => $element
                ];
            }
        } 
        return $links;
    }
    
    /**
     * Method addRoutePrefix
     *
     * @param $route 
     *
     * @return string
     */
    public function addRoutePrefix($route) 
    {
        $explode = explode('_', $route);
        
        return end($explode);
    }

}