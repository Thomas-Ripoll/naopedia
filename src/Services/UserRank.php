<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services;

use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Description of UserRank
 *
 * @author thomas
 */
class UserRank extends AbstractExtension{
    
    private $userRanks;
    public function __construct($userRanks) {
        $this->userRanks = $userRanks;
    }
    public function getFilters()
    {
        return array(
            new TwigFilter('rank', array($this, 'userRank')),
        );
    }
    public function userRank(User $user){
        
        $nbObs = count($user->getObservations());
        $rank = [
            "statut"=>null,
            "image"=>null,
            "next"=>null,
            "left"=>null,
        ];
        foreach($this->userRanks as $key => $value){
            if($key<=$nbObs ){
                $rank["statut"] = $value["statut"];
                $rank["image"] = $value["image"];
            }
            if($key>$nbObs){
                $rank["left"] = $key-$nbObs;
                $rank["next"] = $value["statut"];
                break;
            }
        }
        
        
        return $rank;
    }
    
}
