<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;

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
}
