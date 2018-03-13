<?php

/*
*
*/

namespace App\Services;

use App\Entity\Bird;
use App\Entity\Image;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
* Description of TrendImage
*
* @author thomas
*/
class TrendImage extends AbstractExtension{
    
    private $em;
    public function __construct(\Doctrine\ORM\EntityManagerInterface $em) {
        $this->em = $em;
    }
    public function getFilters()
    {
        return array(
            new TwigFilter('trendImage', array($this, 'trendImage')),
        );
    }
    public function trendImage(Bird $bird){
        
        $image = $this->em->getRepository(\App\Entity\Image::class)->findByTrend($bird);
        $url=(is_null($image))? '"images/"bird.png': ((!strpos($image->getUrl(),"http"))?'"images/"uploads/':"").$image->getUrl();

        return $url;
    }
    
}
