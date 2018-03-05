<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Listeners;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Description of PrePersistListener
 *
 * @author thomas
 */
class PrePersistArticleListener {
    private $security_context;
    public function __construct(TokenStorageInterface $security_context) {
       
        $this->security_context = $security_context;
             
    }
    public function prePersist(LifecycleEventArgs $args){
        $entity = $args->getEntity();
        
        if (!$entity instanceof \App\Entity\Article && !$entity instanceof \App\Entity\Image ) {
            return;
        }
        
        $user = ($this->security_context->getToken() != null)?
                $this->security_context->getToken()->getUser():
                null;
        $entity->setAuthor($user);
    }
}
