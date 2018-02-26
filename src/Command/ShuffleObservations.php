<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ShuffleObservations
 *
 * @author thomas
 */
class ShuffleObservations extends Command {
    
    private $em;
    public function __construct(\Doctrine\ORM\EntityManagerInterface $em) {
        $this->em = $em;
         parent::__construct();
    }
    protected function configure() {
        $this
                // the name of the command (the part after "bin/console")
                ->setName('app:shuffle-obs')

                // the short description shown while running "php bin/console list"
                ->setDescription('Shuffles observations dates.')

                // the full command description shown when running the command with
                // the "--help" option
                ->setHelp('This command allows you to shuffle observation dates...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        
        $output->writeln([
            'Suffeling dates',
            '============',
            '',
        ]);
        $observations = $this->em->getRepository(\App\Entity\Observation::class)->findAll();
        $todaytimestamp = (new \DateTime())->getTimestamp();
        $threenyearsenviron = $todaytimestamp - (1000 * 24 * 3600) ;
        foreach($observations as $obs){
            $obsdate = (new \DateTime())->setTimestamp(rand($threenyearsenviron, $todaytimestamp));
            $obs->setDate($obsdate);
            $this->em->persist($obs);
        }
        $this->em->flush();
      
    }

}
