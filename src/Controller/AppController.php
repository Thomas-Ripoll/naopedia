<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Observation;
use App\Entity\Image;
use App\Entity\Bird;
use App\Form\ObservationType;
use App\Form\ImageType;

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
    * @Route("/user/{username}", name="userpage")
    */
    public function userpage($username)
    {
      $em = $this->getDoctrine()->getManager();
      $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);

      $observations = $em->getRepository(Observation::class)->findBy(['user' => $user->getId()]);


      if (!$user) {
          $this->get('session')->getFlashBag()->add('alert', 'Il n\'y a pas d\'utilisateur à ce nom');
          return $this->render("userpage.html.twig");
      }
      else{
       return $this->render("userpage.html.twig", array(
           'user' => $user,
          'observations' => $observations ));
      }
    }

    /**
    * @Route("/post", name="post")
    */
    public function postObservation(Request $request)
    {

      $observation = new Observation();
      $form = $this->createForm(ObservationType::class, $observation);

      $observation->setUser($this->getUser());
      $observation->setValid(FALSE);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {

        // IMAGE GENERATION
        $img = $observation->getImage()->getUrl();
        // Generate a unique name for the file before saving it
        $imgName = md5(uniqid()).'.'.$img->guessExtension();
        // Move the file to the directory where brochures are stored
        $img->move(
          $this->getParameter('post_directory'),$imgName
        );
        // Update the 'brochure' property to store the PDF file name
        // instead of its contents
        $observation->getImage()->setUrl($imgName);

        //Matching with Bird Entity
        $em = $this->getDoctrine()->getManager();

        $em->persist($observation);
        $em->flush();

        $this->addFlash(
          'notice',
          'une observation a été ajouté '
        );

        return $this->redirect('/');
      }

      return $this->render('post.html.twig',[
        'form' => $form->createView()
      ]);


    }

}
