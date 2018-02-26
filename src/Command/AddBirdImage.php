<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Command;

/**
 * Description of AddBirdImage
 *
 * @author thomas
 */
class AddBirdImage {
    private $em;
    public function __construct(\Doctrine\ORM\EntityManagerInterface $em) {
        $this->em = $em;
        parent::__construct();
    }
    protected function configure() {
        $this
                // the name of the command (the part after "bin/console")
                ->setName('app:addbirdimage')

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
        $obss=$this->em->getRepository(\App\Entity\Observation::class)->findAll();
        foreach($obss as $obs){
            $bird = $obs->getBird();
            $bird->addImage($obs->getImage());
            $this->em->persist($bird);
        }
        $this->em->flush();
        
        
      
    }
}
