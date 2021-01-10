<?php

namespace App\Listener;

use App\Response\AddInEmptyResponse;
use App\Response\AddInFullyResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ResponseSubscriber implements EventSubscriberInterface
{        
    /**
     * addInEmptyResponse
     *
     * @var AddInEmptyResponse
     */
    private $addInEmptyResponse;
    
    /**
     * addInFullyResponse
     *
     * @var AddInFullyResponse
     */
    private $addInFullyResponse;
    
    /**
     * client
     *
     * @var UserInterface
     */
    private $client;

    public function __construct(TokenStorageInterface $token, 
        AddInEmptyResponse $addInEmptyResponse, AddInFullyResponse $addInFullyResponse
    ) {
        if ($token->getToken() != null) {
            $this->client = $token->getToken()->getUser();
        }
        $this->addInEmptyResponse = $addInEmptyResponse;
        $this->addInFullyResponse = $addInFullyResponse;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        //$this->client = $event->getRequest()->getUser();
        
        $this->route = $event->getRequest()->get('_route');

        $content = $event->getResponse()->getContent();
        $content = json_decode($content,true);
        
        //For error message - 400, 404, 401, 500, ...
        if (!in_array($event->getResponse()->getStatusCode($content), [200, 201, 204])) {
            return;

        //For empty response - 200, 201, 204
        } elseif ($content != null && !array_key_exists('data', $content)) {
            
            $newContent = $this->addInEmptyResponse->addInResponse($content);
        } else {
            
            $newContent = $this->addInFullyResponse->addInResponse($content); 
        }

        if ($this->client != null) {
            $newContent['current_client'] = $this->formatCurrentClient($event);
        }
        
        //Response for APi DOC - Nelmio bundle
        if ($this->route == 'app.swagger_ui') {
            return $event->getResponse();
        } 

        $newContent = json_encode($newContent);
        $event->getResponse()->setContent($newContent);
    }
    
    /**
     * Method formatCurrentClient
     * This method add information of the current user in the content of the response
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
