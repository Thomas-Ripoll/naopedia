<?php

namespace App\Services;

use App\Entity\User;

/**
  *Service for mail
 */
class Mailer {

  private $mailer;
  private $templating;

  public function __construct(\Swift_Mailer $mailer, \Twig_Environment $templating)
  {
    $this->mailer = $mailer;
        $this->templating = $templating;

  }

  public function sendNewUser($user)
  {

    $message = (new \Swift_Message('Hello Email'))
        ->setFrom('naopegia@gmail.com')
        ->setTo($user->getEmail())
        ->setBody(
             $this->templating->render(
                // templates/emails/registration.html.twig
                'emails/signin.html.twig',
                array('user' => $user)
            ),
            'text/html'
        )
        /*
         * If you also want to include a plaintext version of the message
        ->addPart(
            $this->renderView(
                'emails/registration.txt.twig',
                array('name' => $name)
            ),
            'text/plain'
        )
        */
    ;
    $this->mailer->send($message);
  }

}
