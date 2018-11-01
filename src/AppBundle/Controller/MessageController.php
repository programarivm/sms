<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends FOSRestController
{
    public function listAction()
    {
        $data = [
            'status' => 200,
            'message' => 'Foo'
        ];
        $view = $this->view($data, 200);

        return $this->handleView($view);
    }

    public function sendAction()
    {
        // TODO ...

        $data = [
            'status' => Response::HTTP_OK,
            'message' => 'Message successfully queued'
        ];
        $view = $this->view($data, 200);

        return $this->handleView($view);
    }
}
