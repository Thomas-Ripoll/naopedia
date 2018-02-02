<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

class AppController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        // replace this line with your own code!
       return $this->render("base.html.twig");
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin()
    {

    }

    /**
    * @Route("/user/{username}", name="userpage")
    */
    public function userpage($username)
    {
      $em = $this->getDoctrine()->getManager();
      $repository = $this->getDoctrine()->getRepository(User::class);
      $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);


      if (!$user) {
          $this->get('session')->getFlashBag()->add('alert', 'Il n\'y a pas d\'utilisateur à ce nom');
          return $this->render("userpage.html.twig");
      }
      else{
       return $this->render("userpage.html.twig", array(
           'user' => $user));
      }
    }

}
