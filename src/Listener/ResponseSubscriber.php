<?php

namespace App\Listener;

use App\Response\FormatResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ResponseSubscriber implements EventSubscriberInterface
{    
    /**
     * manager
     *
     * @var EntityManagerInterface;
     */
    private $manager;
    
    /**
     * route
     *
     * @var string
     */
    private $route;
    
    /**
     * event
     *
     * @var ResponseEvent
     */
    private $event;
    
    /**
     * normalizer
     *
     * @var NormalizerInterface
     */
    private $normalizer;

    public function __construct(EntityManagerInterface $manager, NormalizerInterface $normalizer)
    {
        $this->manager = $manager;
        $this->normalizer = $normalizer;
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
        $this->event = $event;

        $this->route = $event->getRequest()->get('_route');

        $content = $this->event->getResponse()->getContent();
        $content = json_decode($content,true);
        
        $routeSuffix = explode('_', $this->route);
        
        //For error message - 400, 404, 401, 500, ...
        if (!in_array($event->getResponse()->getStatusCode($content), [200, 201, 204])) {
            return;
            $newContent = $this->addInErrorMessage();

        //For empty response - 200, 201, 204
        } elseif (!array_key_exists('data', $content)) {
            $newContent = $this->addInEmptyResponse($content);

        //If the response is a list of elements
        } elseif ($routeSuffix[0] == 'list') {
            $newContent = $this->addInListElements($content);

        } else {
            return;
        }

        $newContent = json_encode($newContent);
        $event->getResponse()->setContent($newContent);
    }

    private function addInErrorMessage()
    {
        return;
    }

    private function addInListElements($content)
    {
        foreach ($content['data'] as $item => $details) {
            $uri = $this->event->getRequest()->getUri();

            //We remove parameters 
            $uri = explode('?', $uri)[0]. '/' . $content['data'][$item]['id'];
            //we add id
            $content['data'][$item]['_link']['self'] = "GET " . $uri;   
        }
        return $content;
    }

    private function addInEmptyResponse($content)
    {
        $explode = explode('_', $this->route);
        
        $className = substr(ucfirst(end($explode)), 0, -1);

        $uri = $this->event->getRequest()->getUri();
        
        if (class_exists('App\Entity\\' . $className)) {
            $repo = $this->manager->getRepository('App\Entity\\' . $className);

            if ($this->event->getRequest()->getMethod() == 'POST') {
                $object = $repo->findLastId();
                
                $objectNormalize = $this->normalizer->normalize($object, null, [
                    'groups' => 'show_' . strtolower($className) 
                ]);

                $content['_created']['data'] = $objectNormalize;
                
            }

            if (in_array($this->event->getRequest()->getMethod(), ['PUT', 'PATCH'])) {
                
                $id = explode('/', $uri);
                $id = end($id);
                $object = $repo->find($id);

                $objectNormalize = $this->normalizer->normalize($object, null, [
                    'groups' => 'show_' . strtolower($className) 
                ]);

                $content['_modify']['data'] = $objectNormalize;
                
            }

            $uri = explode('?', $uri)[0]. '/' . $object->getId();
                
            $content['_links']['self'] = "GET " . $uri;
        } 

        return $content;
    }
    
}
