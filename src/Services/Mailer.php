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
        ->setFrom('naopedia@gmail.com')
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

  public function sendResetPassword($user)
  {

    $message = (new \Swift_Message('Reset de mot de passe'))
        ->setFrom('naopedia@gmail.com')
        ->setTo($user->getEmail())
        ->setBody(
             $this->templating->render(
                // templates/emails/registration.html.twig
                'emails/resetPassword.html.twig',
                array('user' => $user)
            ),
            'text/html'
        );
    $this->mailer->send($message);
  }

  public function  sendObservatioValid($observation)
  {

    $message = (new \Swift_Message('Observation validée'))
        ->setFrom('naopegia@gmail.com')
        ->setTo($observation->getUser()->getEmail())
        ->setBody(
             $this->templating->render(
                'emails/email-validation.html.twig',
                array('observation' => $observation)
            ),
            'text/html'
        );
    $this->mailer->send($message);
  }

  public function  sendObservatioRefuse($observation)
  {

    $message = (new \Swift_Message('Observation Refusée'))
        ->setFrom('naopegia@gmail.com')
        ->setTo($observation->getUser()->getEmail())
        ->setBody(
             $this->templating->render(
                'emails/email-refus.html.twig',
                array('observation' => $observation)
            ),
            'text/html'
        );
    $this->mailer->send($message);
  }

  public function  sendContributionRefuse($bird)
  {

    $message = (new \Swift_Message('Contribution Refusée'))
        ->setFrom('naopegia@gmail.com')
        ->setTo($bird->getContributor()->getEmail())
        ->setBody(
             $this->templating->render(
                'emails/email-refus.html.twig',
                array('bird' => $bird)
            ),
            'text/html'
        );
    $this->mailer->send($message);
  }

  public function  sendContributionValid($bird)
  {

    $message = (new \Swift_Message('Contribution Refusée'))
        ->setFrom('naopegia@gmail.com')
        ->setTo($bird->getContributor()->getEmail())
        ->setBody(
             $this->templating->render(
                'emails/email-validation.html.twig',
                array('bird' => $bird)
            ),
            'text/html'
        );
    $this->mailer->send($message);
  }

  public function  sendContact($name, $surname, $email, $mobile, $subject, $message)
  {
    $message = (new \Swift_Message($subject))
        ->setFrom('naopegia@gmail.com')
        ->setTo($email)
        ->setBody(
            $this->templating->render(
                'emails/contact.html.twig',
                array(
                    'name' => $name,
                    'surname'=>$surname,
                    'email'=>$email,
                    'mobile'=>$mobile,
                    'message'=>$message)
            ),
            'text/html'
        );
    $this->mailer->send($message);
  }

  public function  sendGoldenbook($user)
  {
    $message = (new \Swift_Message("Le livre d'or"))
        ->setFrom('naopegia@gmail.com')
        ->setTo($user->getEmail())
        ->setBody(
            $this->templating->render('emails/email-livre-nao.html.twig'),'text/html');
    $this->mailer->send($message);
  }


}
