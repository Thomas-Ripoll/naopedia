<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
      $repository = $this->getDoctrine()->getRepository(User::class);

      // find *all* User objects
      $users = $repository->findAll();

       return $this->render("admin.html.twig", array(
           'users' => $users));
      }

}
