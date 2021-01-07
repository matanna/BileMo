<?php 

namespace App\Response;

class AddResponseLinks
{
    public function AddInEmptyResponse($event) 
    {
        $explode = explode('_', $this->route);
        
        $className = substr(ucfirst(end($explode)), 0, -1);

        $uri = $event->getRequest()->getUri();
    }
}