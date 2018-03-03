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

    $message = (new \Swift_Message('Bienvenue'))
        ->setFrom('naopegia@gmail.com')
        ->setTo($user->getEmail())
        ->setBody(
             $this->templating->render(
                // templates/emails/registration.html.twig
                'emails/signin.html.twig',
                array('user' => $user)
            ),
            'text/html'
        );
    $this->mailer->send($message);
  }

  public function  sendObservatioValid($observation)
  {

    $message = (new \Swift_Message('Observation validée')) //???
        ->setFrom('naopegia@gmail.com')
        ->setTo($user->getEmail())
        ->setBody(
             $this->templating->render(
                'emails/observationValid.html.twig',
                array('observation' => $observation)
            ),
            'text/html'
        );
    $this->mailer->send($message);
  }

}
