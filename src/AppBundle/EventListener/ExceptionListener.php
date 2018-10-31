<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ExceptionListener.
 */
class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $response = new Response();
        $response->setStatusCode($exception->getStatusCode());
        $response->headers->set('Content-Type', 'application/json');

        if ($exception instanceof NotFoundHttpException) {
            $message = 'Not Found';
        }

        $response->setContent(
            json_encode([
                'status' => $exception->getStatusCode(),
                'message' => $message
            ])
        );

        $event->setResponse($response);
    }
}
