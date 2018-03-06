<?php
namespace App\Listeners;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AjaxSecurityListener
 *
 * @author thomas
 */

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AjaxSecurityListener {
    private $twig;
    public function __construct(\Twig_Environment $twig) {
        $this->twig = $twig;
    }
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // only check master request
        if (!$event->isMasterRequest())
            return;

        // get variables
        $exception = $event->getException();
        $request = $event->getRequest();
        // replace Security Bundle 403 with Symfony 403
        if($exception instanceof AccessDeniedException && $request->isXmlHttpRequest())
             $event->setResponse(new \Symfony\Component\HttpFoundation\JsonResponse(["state"=>"connect"],403));
            //throw new AccessDeniedHttpException("Symfony 403 error thrown instead of 403 error of the security bundle");
    }
}
