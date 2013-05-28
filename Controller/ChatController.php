<?php

namespace Cunningsoft\ChatBundle\Controller;

use Cunningsoft\ChatBundle\Entity\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/chat")
 */
class ChatController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/{channel}", name="cunningsoft_chat_show")
     * @Template
     */
    public function showAction($channel = 'default')
    {
        return array(
            'updateInterval' => $this->container->getParameter('cunningsoft_chat.update_interval'),
            'channel' => $channel
        );
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @Route("/post/{channel}", name="cunningsoft_chat_post")
     */
    public function postAction(Request $request, $channel = 'channel')
    {
        $message = new Message();
        $message->setAuthor($this->getUser());
        $message->setChannel($channel);
        $message->setMessage($request->get('message'));
        $message->setInsertDate(new \DateTime());
        $this->getDoctrine()->getManager()->persist($message);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/list/{channel}", name="cunningsoft_chat_list")
     * @Template
     */
    public function listAction(Request $request, $channel = 'default')
    {
        $messages = $this->getDoctrine()->getRepository('CunningsoftChatBundle:Message')->findBy(
            array('channel' => $channel),
            array('id' => 'desc'),
            $this->container->getParameter('cunningsoft_chat.number_of_messages')
        );

        return array(
            'messages' => $messages,
        );
    }
}
