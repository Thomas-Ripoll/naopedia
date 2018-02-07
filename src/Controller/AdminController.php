<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Observation;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;


class AdminController extends BaseAdminController
{
  public function checkAction()
     {
       // controllers extending the base AdminController get access to the
       // following variables:
       //   $this->request, stores the current request
       //   $this->em, stores the Entity Manager for this Doctrine entity

       $id = $this->request->query->get('id');
       $observation = $this->em->getRepository(Observation::Class)->find($id);

       return $this->render("checkObservation.html.twig", array(
          'observation' => $observation ));
      }

      /**
       * @Route("/admin/valid{observationId}", name="valid")
       */
      public function observationAction($observationId)
      {
         $em = $this->getDoctrine()->getManager();
         $observation = $em->getRepository(Observation::Class)->find($observationId);

         $observation->setValid(True);
         $em->persist($observation);
         $em->flush();
          return $this->redirectToRoute('admin');
      }

      /**
       * @Route("/admin/refuse{observationId}", name="refuse")
       */
      public function RefuseAction($observationId)
      {
          return $this->redirectToRoute('admin');
      }



}
