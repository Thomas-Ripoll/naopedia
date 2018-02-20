<?php

namespace App\DataFixtures;

use App\Entity\Bird;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AppFixtures
 *
 * @author thomas
 */
class AppFixtures extends Fixture{
   
    
    public function load(ObjectManager $manager) {
            
        
            $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder(";")]);
            
            $rawcontent = file_get_contents(__DIR__.'/../ressources/TAXREF.csv');
            
            $content = mb_convert_encoding($rawcontent, 'UTF-8', mb_detect_encoding($rawcontent, 'UTF-8, ISO-8859-1', true));
            
            $data = $serializer->decode($content, 'csv');
            $i = 0;
            $selectedBird = null;
            foreach($data as $birdData){
                
                $bird = new Bird();
                $bird->setName(trim($birdData["NOM_VERN"]));
                $bird->setLatinName(trim($birdData["LB_NOM"]));
                $bird->setFamille(trim($birdData["FAMILLE"]));
                $bird->setOrdre(trim($birdData["ORDRE"]));
                if($i == 3300){
                    $selectedBird = $bird;
                }
                $manager->persist($bird);
                
                $i++;
                
            }
            $user = new User();
            $user->setUsername("toto");
            $user->setPassword('$2y$12$xCFO3tMmzKBR5/tcFVa77Ow3Mi8NFJwkUZunT8RXiAuejbiYoXF5O');
            $user->setAvatar('82fbfc3a2bbfd1062dddc0ae8d5d1630.jpeg');
            $user->setEmail('ripoll@gmail.com');
            $user->setSalt('HIb1ru28S4UA5FWerfI6F3C');
            $user->setRoles(["ROLE_ADMIN"]);
            $manager->persist($user);
            
            $user = new User();
            $user->setUsername("solario");
            $user->setPassword('36abf0cbd6b9d4ddfbfa0aafadf65caa.png');
            $user->setAvatar('36abf0cbd6b9d4ddfbfa0aafadf65caa.png');
            $user->setEmail('solario@outlook.fr');
            $user->setSalt('3LoBzJeXGH6EoBXOXxXQ+6K');
            $user->setRoles(["ROLE_ADMIN"]);
            $manager->persist($user);
            
            
            
            
            $manager->flush();
            
            

}
}
