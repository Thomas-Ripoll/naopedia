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
class AppFixtures extends Fixture {

    private $proxy = null;
   private $used = [];
    public function load(ObjectManager $manager) {


        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder(";")]);

        $rawcontent = file_get_contents(__DIR__ . '/../ressources/TAXREF.csv');

        $content = mb_convert_encoding($rawcontent, 'UTF-8', mb_detect_encoding($rawcontent, 'UTF-8, ISO-8859-1', true));

        $data = $serializer->decode($content, 'csv');

        $user = new User();
        $user->setUsername("toto");
        $user->setPassword('$2y$12$xCFO3tMmzKBR5/tcFVa77Ow3Mi8NFJwkUZunT8RXiAuejbiYoXF5O');
        $user->setAvatar('82fbfc3a2bbfd1062dddc0ae8d5d1630.jpeg');
        $user->setEmail('ripoll@gmail.com');
        $user->setSalt('HIb1ru28S4UA5FWerfI6F3C');
        $user->setRoles(["ROLE_ADMIN"]);
        $manager->persist($user);
        $birdNum = 1;
        foreach ($data as $birdData) {

            $bird = new Bird();
            $bird->setName(trim($birdData["NOM_VERN"]));
            $bird->setLatinName(trim($birdData["LB_NOM"]));
            $bird->setFamille(trim($birdData["FAMILLE"]));
            $bird->setOrdre(trim($birdData["ORDRE"]));
            $manager->persist($bird);
            $manager->flush();

           /* $max = rand(25, 50);


            $imagedata = $this->getData($max, $bird->getSlug());


            //dump($imagedata);
            //dump('https://api.qwant.com/api/search/images?count='.$max.'&offset=1&q='.$bird->getSlug());
            dump($birdNum);
            dump(!is_null($imagedata) && $imagedata->status == "success");
            $images = (!is_null($imagedata) && $imagedata->status == "success") ? $imagedata->data->result->items : [];

            for ($i = 0; $i < $max; $i++) {

                $obs = new \App\Entity\Observation();

                if (count($images) > 0 && strlen($images[$i % count($images)]->media) <= 255) {
                    $desc = substr($images[$i % count($images)]->title, 0, min(strlen($images[$i % count($images)]->title), 199));
                    $img = new \App\Entity\Image();
                    $img->setUrl($images[$i % count($images)]->media);
                    $img->setAlt($desc);
                    $img->setAuthor($user);
                    $obs->setImage($img);
                    $obs->setDescription($desc);
                } else {

                    $obs->setDescription(" ");
                }
                $obs->setBird($bird);
                $obs->setGeoloc([
                    (rand(0, 800) / 100) + 42,
                    (rand(0, 1300) / 100) - 5
                ]);

                $obs->setUser($user);
                $manager->persist($obs);
            }*/
            $birdNum++;
            //$manager->flush();
        }


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
    public function getProxy(){
        if(is_null($this->proxy)){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://gimmeproxy.com/api/getProxy?get=true&user-agent=true&supportsHttps=true&protocol=https&anonymityLevel=1');
            curl_setopt($ch, CURLOPT_HEADER, false); // Assuming you're requesting JSON
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
            //dump($index);
            $response = curl_exec($ch);
            curl_close($ch);
            $proxydata = json_decode($response);
            dump($proxydata);
            //dump($proxydata->ipPort);
            $this->proxy = $proxydata;
        }
        return $this->proxy;
    }
    public function getData($max, $query) {

        
        //dump(count($this->proxy));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.qwant.com/api/search/images?count=' . $max . '&offset=1&q=' . $query);
        curl_setopt($ch, CURLOPT_PROXY, $this->getProxy()->ipPort);
        curl_setopt($ch, CURLOPT_HEADER, false); // Assuming you're requesting JSON
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
        //dump($index);
        $response = curl_exec($ch);
        curl_close($ch);
        $imagedata = json_decode($response);
//dump($imagedata);
        if (is_null($imagedata) || $imagedata->status == "error") {
            dump($imagedata);
            $this->proxy = null;
            return $this->getData($max, $query);
        }
        else{
            dump($this->proxy);
        }
        return $imagedata;
    }

}
