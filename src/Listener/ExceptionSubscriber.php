<?php

namespace App\Listener;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    //404
    const HTTP_NOT_FOUND = 'Symfony\Component\HttpKernel\Exception\NotFoundHttpException';

    //405
    const BAD_METHOD = 'Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException';

    //400
    const NO_BODY_DATA = 'App\Exception\NoJsonBodyException';

    //400
    const NOT_ENCODABLE = 'Symfony\Component\Serializer\Exception\NotEncodableValueException';

    //500
    const NO_CONNEXION = 'Doctrine\DBAL\Exception\ConnectionException';

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $path = $event->getRequest()->getMethod() . ' ' . $event->getRequest()->getUri();

        $exceptionFullName = get_class($event->getThrowable());

        $exceptionName = explode('\\', $exceptionFullName);
        
        $method = 'on' . end($exceptionName);
       
        if (method_exists($this, $method)) {
            $this->$method($event, $path);
        }
    }

    //If error 404 - Page not found
    private function onNotFoundHttpException($event, $path) 
    {
        $response =  new JsonResponse([
            'status' => 404 . ": Page not Found",
            'message' => "La route $path ne correspond à aucune ressource."
        ], 404);

        $event->setResponse($response);
    }

    //If error 405 - Bad method 
    private function onMethodNotAllowedHttpException($event, $path) 
    {  
        $response =  new JsonResponse([
            'status' => 405 . ": Method Not Allowed",
            'message' => "Aucune route disponible pour '$path'."
        ], 405);

        $event->setResponse($response);
    }

    //if json body is null for POST, PUT and PATCH
    private function onNoJsonBodyException($event, $path)
    {   
        $response =  new JsonResponse([
            'status' => 400 . ": Bad Request",
            'message' => "Les données json sont inexistantes dans le body de la requête pour '$path'."
        ], 400);

        $event->setResponse($response);
    }

    //If the serializer or normalizer has a problem
    private function onNotEncodableValueException($event, $path)
    {     
        $response =  new JsonResponse([
            'status' => 400 . ": Bad Request",
            'message' => $event->getThrowable()->getMessage()
        ], 400);

        $event->setResponse($response);
    }

    //If the connexion to the database failed
    private function onConnectionException($event, $path)
    { 
        $response =  new JsonResponse([
            'status' => 500 . ": Internal Serer Error",
            'message' => 'La connexion à la base de données a échouée.'
        ], 500);

        $event->setResponse($response);
    }
}
