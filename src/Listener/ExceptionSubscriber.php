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

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        
        //If error 404
        if (get_class($event->getThrowable()) === self::HTTP_NOT_FOUND) {

            $path = $event->getRequest()->getMethod() . ' ' . $event->getRequest()->getPathInfo();

            $response =  new JsonResponse([
                'status' => 404 . ": Page not Found",
                'message' => "Cette route $path ne correspond à aucune ressource."
            ], 404);

            $event->setResponse($response);
        }

        //if error 405
        if (get_class($event->getThrowable()) === self::BAD_METHOD) {
            
            $path = $event->getRequest()->getMethod() . ' ' . $event->getRequest()->getPathInfo();

            $response =  new JsonResponse([
                'status' => 405 . ": Method Not Allowed",
                'message' => "Aucune route disponible pour '$path'."
            ], 405);

            $event->setResponse($response);
        }

        //if json body is null for POST, PUT and PATCH
        if (get_class($event->getThrowable()) === self::NO_BODY_DATA) {
            
            $path = $event->getRequest()->getMethod() . ' ' . $event->getRequest()->getPathInfo();

            $response =  new JsonResponse([
                'status' => 400 . ": Bad Request",
                'message' => "Les données json sont inexistantes dans le body de la requête pour '$path'."
            ], 400);

            $event->setResponse($response);
        }

        if (get_class($event->getThrowable()) === self::NOT_ENCODABLE) {
            
            $response =  new JsonResponse([
                'status' => 400 . ": Bad Request",
                'message' => $event->getThrowable()->getMessage()
            ], 400);

            $event->setResponse($response);
        }
    }

}
