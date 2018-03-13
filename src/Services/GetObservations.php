<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services;

use App\Entity\Observation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Description of GetObservations
 *
 * @author thomas
 */
class GetObservations{
    
    private $vichHelper;
    private $securityContext;
    
    public function __construct(  TokenStorageInterface $security_context, UploaderHelper $vichHelper) {
       
        $this->securityContext = $security_context;
        $this->vichHelper =$vichHelper;
        
    }
    public function generateObservations($observations,$qs){
        
        
        $datesArray = [];
        $user = ($this->securityContext->getToken()->getUser() != "anon.")?
                $this->securityContext->getToken()->getUser():
                null;
        
        
        
        foreach ($observations as $obs) {
            $img = $obs->getImage();
            if (!key_exists($obs->getSearchDate(), $datesArray)) {
                $datesArray[$obs->getSearchDate()] = [];
            }
            $datesArray[$obs->getSearchDate()][] = [
                "id" => $obs->getId(),
                "lat" => $obs->getGeoloc("lat"),
                "lng" => $obs->getGeoloc("lng"),
                "url" => (!is_null($img)) ? $img->getUrl() : "",
                "caption" => $obs->getDescription(),
                "author" => $obs->getUser()->getUsername(),
                "day" => $obs->getDate()->format("j"),
                "date" => $obs->getDate()->format("d/m/Y"),
                "birdName" => $obs->getBird()->getLatinName(),
                "birdSlug" => $obs->getBird()->getSlug(),
                "img" => (!is_null($img)) ? [
                    "id" => $img->getId(),
                    "url" => (preg_match("/http/", $img->getUrl())) ? $img->getUrl() : $this->vichHelper->asset($img, "imageFile"),
                    "liked" => !is_null($user) && in_array($user->getId(), $img->getLikes()),
                    "countLikes" => count($img->getLikes())
                    ] : null
            ];
        }
        if (key_exists("dates", $qs["filters"])) {
            foreach ($qs["query"]["dates"] as $date) {
                if (!key_exists($date, $datesArray)) {
                    $datesArray[$date] = [];
                }
            }
        }
        return $datesArray;
    }
    
}
