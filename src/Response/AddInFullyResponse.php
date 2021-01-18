<?php

namespace App\Response;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AddInFullyResponse
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
     * route
     *
     * @var string
     */
    private $route;
    
    /**
     * uri
     *
     * @var string
     */
    private $uri;
    
    /**
     * router
     *
     * @var RouterInterface
     */
    private $router;

    public function __construct(RequestStack $request, RouterInterface $router)
    {
        $this->route = $request->getCurrentRequest()->get('_route');
        $this->uri = $request->getCurrentRequest()->getUri();
        $this->router = $router;
    }
    
    /**
     * Method addInResponse
     * This method return the content of the response with added informations
     *
     * @param $existingContent 
     *
     * @return void
     */
    public function addInResponse($existingContent)
    {
        $routeSuffix = explode('_', $this->route);

        if ($routeSuffix[0] == 'list') {
            $newContent = $this->addInListElements($existingContent);


        } elseif ($routeSuffix[0] == 'show') {
            $newContent = $this->addInOneElement($existingContent);

        } else {
            return;
        }

        return $newContent;
    }
  
    /**
     * Method addInListElements
     * This method add links (GET) for each element of the page
     *
     * @param $content 
     *
     * @return void
     */
    private function addInListElements($content)
    {
        $explode = explode('_', $this->route);
        $resource = end($explode);

        foreach ($content['data'] as $item => $details) {

            //We remove parameters 
            $uri = explode('?', $this->uri)[0]. '/' . $content['data'][$item]['id'];
            //we add id
            $content['data'][$item]['_link']['self'] = [
                "Href" => $uri,
                "Rel" => $this->route,
                "Method" => "GET"
            ];   
        }
        if ($resource === 'users') {
            $content['data']['_link']['add'] = [
                "Href" => 'https://127.0.0.1:8000/users',
                "Rel" => 'add_users',
                "Method" => "POST"
            ];
        }
        return $content;
    }
    
    /**
     * Method addInOneElement
     * This method add all avaibale links for the element display in the page
     *
     * @param $content 
     *
     * @return array
     */
    private function addInOneElement($content)
    {
        //We get resource name by the route
        $explode = explode('_', $this->route);
        $resource = end($explode);
        
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
        
        if ($links != null) {
            $content['_links'] = $links;
        }

        return $content;
    }
    
}