<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Controller\TokenAuthenticatedController;
use Firebase\JWT\JWT;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class TokenSubscriber implements EventSubscriberInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof TokenAuthenticatedController) {
            $authorization = $event->getRequest()->headers->get('Authorization');
            $jwt = trim(preg_replace('/^(?:\s+)?Bearer\s/', '', $authorization));
            try {
                $decoded = JWT::decode($jwt, $this->container->getParameter('jwt_secret'), ['HS256']);
            } catch (\Exception $e) {
                throw new UnauthorizedHttpException('Unauthorized');
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
        );
    }
}
