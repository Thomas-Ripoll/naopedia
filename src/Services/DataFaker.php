<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;

/*
 * 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DataFaker
 *
 * @author thomas
 */
class DataFaker {

    private $proxies = null;
    private $em;

    public function __construct(\Doctrine\ORM\EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function generateProxies() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://free-proxy-list.net/');
        curl_setopt($ch, CURLOPT_HEADER, false); // Assuming you're requesting JSON
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
        //dump($index);
        $response = curl_exec($ch);

        $crawler = new Crawler($response);

        $this->proxies = $crawler->filter('#proxylisttable tbody tr')->each(function(Crawler $node, $i) {
            return $node->filter("td")->eq(0)->text() . ':' . $node->filter("td")->eq(1)->text();
        });
    }

    public function getProxy() {

        if (is_null($this->proxies) || count($this->proxies) <= 0) {
            $this->generateProxies();
        }
        return array_shift($this->proxies);
    }

    public function getData($query, $needProxy = false) {


        //dump(count($this->proxies));
        //dump($proxy);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.qwant.com/api/search/images?count=50&offset=1&q=' . urlencode($query));
        if ($needProxy) {
            $proxy = $this->getProxy();
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        }
        curl_setopt($ch, CURLOPT_HEADER, false); // Assuming you're requesting JSON
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
        //dump($index);
        $response = curl_exec($ch);
        curl_close($ch);
        $imagedata = json_decode($response);



        return $imagedata;
    }

    public function getfakeObservations($bird_slug) {
        $users = $this->em->getRepository(\App\Entity\User::class)->findAll();

        $bird = $this->em->getRepository(\App\Entity\Bird::class)->find($bird_slug);
        ///dump($bird_slug);
        $max = rand(25, 50);
        $imagedata = $this->getData($bird->getLatinName());
        while (is_null($imagedata) || $imagedata->status == "error") {

            $imagedata = $this->getData($bird->getLatinName(), true);
        }




        $images = $imagedata->data->result->items;
        //dump($images);
        $observations = [];
        
        $todaytimestamp = (new \DateTime())->getTimestamp();
        $threenyearsenviron = $todaytimestamp - (1000 * 24 * 3600) ;
        for ($i = 0; $i < min($max, count($images)); $i++) {



            $obs = new \App\Entity\Observation();

            if (count($images) > 0 && strlen($images[$i]->media) <= 255) {
                $desc = substr($images[$i]->title, 0, min(strlen($images[$i % count($images)]->title), 199));
                $img = new \App\Entity\Image();
                $img->setUrl($images[$i]->media);
                $img->setAlt($desc);
                $img->setAuthor($users[rand(0, count($users) - 1)]);
                $obs->setImage($img);
                $obs->setDescription($desc);
                $bird->addImage($img);
            } else {

                $obs->setDescription(" ");
            }
            
            
            $obsdate = (new \DateTime())->setTimestamp(rand($threenyearsenviron, $todaytimestamp));
            $obs->setDate($obsdate);
            $obs->setBird($bird);
            $obs->setGeoloc([
                (rand(0, 800) / 100) + 42,
                (rand(0, 1300) / 100) - 5
            ]);

            $obs->setUser($users[rand(0, count($users) - 1)]);

            $this->em->persist($obs);
            $observations[] = $obs;
        }
        $this->em->flush();
        return $observations;
    }

}
