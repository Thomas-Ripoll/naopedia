<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services;

/**
 * Description of QueryStringDecoder
 *
 * @author thomas
 */
class QueryStringDecoder {
    
    private $em;
    private $request;
    public function __construct(\Doctrine\ORM\EntityManagerInterface $em, \Symfony\Component\HttpFoundation\RequestStack $request) {
        $this->em  = $em;
        $this->request = $request->getMasterRequest();
    }
    
    
    private function buildDatesArray($date1, $date2){
        $date1 = \DateTime::createFromFormat("d-m-Y", $date1);
        $date2 = \DateTime::createFromFormat("d-m-Y", $date2);
        $datesArray = [];
        $date2Format = $date2->format("Ym");
        $date1Format = "";
        $i= 0;
        do{
            $date1Format = $date1->format("Ym");
            $datesArray[] = $date1Format;
            $date1->modify('first day of +1 month');
            $i++;
            if($i>20){
                break;
            }
        }while($date1Format != $date2Format);
        return $datesArray;
    }
    /**
     * 
     * Décode la queryString
     * 
     * @param type $qs
     */
    public function decode(){
        return 
            ($this->request->getMethod() == "POST")?
                $this->decodePost($this->request->request):
                $this->decodeUrl($this->request->query);
        
    }
    private function decodePost($qs){
        $parameter= ["query"=>[],"filters"=>[]];
        if($qs->has("bird"))
        {
            $bird = $this->em->getRepository(\App\Entity\Bird::class)->find($qs->get("bird"));
            $parameter["query"]["bird"] = $bird->getId();
            $parameter["filters"]["bird"] = [
                "birdId"=>$bird->getId(),
                "birdSlug"=>$bird->getSlug(),
                "birdName"=>$bird->getName(),
                "birdLatinName"=>$bird->getLatinName(),
                
            ];
        }
        if($qs->has("dates"))
        {
            $parameter["query"]["dates"] = $this->decodeDates($qs->get("dates"));
            dump($parameter["query"]);
           
        }
        if(!$qs->has("bird") && (!$qs->has("dates") || ( $qs->has("dates") && count($parameter["query"]["dates"])>4 ))){
            throw new \Exception("Le nombre de mois ne peut excéder 4 si aucun oiseau n'est sélectionné");
        }
        return $parameter;
    }
    private function decodeUrl($qs){
         $parameter= ["query"=>[],"filters"=>[]];
        if($qs->has("bird"))
        {
            $bird = $this->em->getRepository(\App\Entity\Bird::class)->findOneBySlug($qs->get("bird"));
            $parameter["query"]["bird"] = $bird->getId();
            $parameter["filters"]["bird"] = [
                "birdId"=>$bird->getId(),
                "birdSlug"=>$bird->getSlug(),
                "birdName"=>$bird->getName(),
                "birdLatinName"=>$bird->getLatinName(),
                
            ];
        }
        if($qs->has("dates"))
        {
            $dates = explode("to",$qs->get("dates"));
            $parameter["query"]["dates"] = call_user_func_array([$this,"buildDatesArray"],$dates);
            $parameter["filters"]["dates"] = $dates;
        }
        if(!$qs->has("bird") && !$qs->has("dates")){
            $threeMonths = (new \DateTime("-3 Months"))->format("d-m-Y");
            $today = (new \DateTime("today"))->format("d-m-Y");
            
            $parameter["query"]["dates"] = $this->buildDatesArray($threeMonths, $today);
            $parameter["filters"]["dates"] = [$threeMonths,$today]; 
        }
        return $parameter;
    }
    private function decodeDates($dates){
        
        $monthsArray = [];
        $yearsSplit = explode("+",$dates);
        $years = [];
        
        
        foreach($yearsSplit as $year){
            
            $yearSplit = explode("(",$year);
            if(count($yearSplit)<=1){
                $monthsArray = array_merge($monthsArray, $this->dateRange($yearSplit[0],1, 12));
            }
            else{
                $months = explode(",",substr($yearSplit[1], 0, -1));
                
                foreach($months as $month){
                    if(strrpos($month, "-")){
                       
                        $month = explode("-", $month);
                        $monthsArray = array_merge($monthsArray, $this->dateRange($yearSplit[0], intval($month[0]), intval($month[1])));
                         //dump($this->dateRange($yearSplit[0], intval($month[0]), intval($month[1])));
                    }
                    else{
                        $monthsArray[] = $yearSplit[0].$month;
                    }
                }
            }
        }
       
        return $monthsArray;
    }
    private function dateRange($year,$from, $to){
        $range = [];
        
        do{
            $range[] = $year.substr("00".$from, -2); 
            $from++;
        }
        while($from <= $to);
        
        return $range;
        
    }
}
