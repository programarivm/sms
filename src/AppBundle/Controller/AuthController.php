<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Firebase\JWT\JWT;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthController extends FOSRestController
{
    public function authAction(Request $request)
    {
        $data = json_decode($request->getContent());

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->findOneBy([
            'username' => $data->username,
            'password' => $data->password
        ]);

        if (!$user) {
            throw new NotFoundHttpException;
        }

        $token = [
            'id' => $user->getId(),
            'exp' => time() + (60 * 60)
        ];

        $data = [
            'status' => Response::HTTP_OK,
            'access_token' => JWT::encode($token, getenv('JWT_SECRET'))
        ];

        $view = $this->view($data, 200);

        return $this->handleView($view);
    }
}
