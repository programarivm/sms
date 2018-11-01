<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends FOSRestController implements TokenAuthenticatedController
{
    public function listingAction()
    {
        $messages = $this->getDoctrine()
            ->getRepository(Message::class)
            ->createQueryBuilder('m')
            ->select('u.id as user_id, m.tel, m.content, m.status, m.publishedAt')
            ->join(User::class, 'u', 'WITH', 'm.author = u.id')
            ->orderBy('m.publishedAt', 'ASC')
            ->getQuery()
            ->getresult();

        $view = $this->view([
            'status' => Response::HTTP_OK,
            'result' => $messages,
        ], Response::HTTP_OK);

        return $this->handleView($view);
    }

    public function sendAction(Request $request)
    {
        $data = json_decode($request->getContent());

        $message = new Message();
        $message->setTel($data->telephone);
        $message->setContent($data->content);

        $validator = $this->get('validator');
        $errors = $validator->validate($message);

        if (count($errors) > 0) {

            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            $view = $this->view([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $errorMessages,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } else {

            $mssg = [
                'telephone' => $data->telephone,
                'content' => $data->content,
            ];

            $this->get('old_sound_rabbit_mq.send_message_producer')->publish(serialize($mssg));

            $view = $this->view([
                'status' => Response::HTTP_OK,
                'message' => 'Message successfully queued',
            ], Response::HTTP_OK);
        }

        return $this->handleView($view);
    }
}
