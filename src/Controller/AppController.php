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


      if (!$user) {
          $this->get('session')->getFlashBag()->add('alert', 'Il n\'y a pas d\'utilisateur à ce nom');
          return $this->redirectToRoute('homepage');
      }
      else{
      $observations = $em->getRepository(Observation::class)->findBy(['user' => $user->getId()]);
       return $this->render("userpage.html.twig", array(
           'user' => $user,
          'observations' => $observations ));
      }
    }

    /**
    * @Route("/bird/{slug}", name="birdpage")
    */
    public function birdPage($slug)
    {
      $em = $this->getDoctrine()->getManager();
      $bird = $em->getRepository(Bird::class)->findOneBy(['slug' => $slug]);


      if (!$bird) {
          $this->get('session')->getFlashBag()->add('alert', 'Il n\'y a pas d\'utilisateur à ce nom');
          return $this->render("birdpage.html.twig");
      }
      else{
       return $this->render("birdpage.html.twig", array(
           'bird' => $bird));
      }
    }
    /**
     * @Route("/carte", name="map")
     */
    public function application(Request $request){
        return $this->render("map.html.twig");
    }
    /**
     * @Route("/get-bird-list", name="bird-search")
     */
    public function birdSearch(Request $request){
        $query = $request->query->get("term");
        $em = $this->getDoctrine()->getManager();
        $birdsList = $em->getRepository(Bird::class)->search($query);
        $birdJson = [];
        foreach($birdsList as $bird){
            $birdJson[] = [
                "birdId" => $bird->getId(),
                "birdName" => $bird->getName(),
                "birdLatinName" => $bird->getLatinName()
            ];
        }
        return $this->json($birdJson);
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

        $img = $observation->getImage()->getUrl();
        // Generate a unique name for the file before saving it
        $imgName = md5(uniqid()).'.'.$img->guessExtension();
        $img->move(
          $this->getParameter('post_directory'),$imgName
        );
        $observation->getImage()->setUrl($imgName);
        $observation->SetValid(FALSE);


        $em = $this->getDoctrine()->getManager();
        $em->persist($observation);
        $em->flush();

        // IMAGE Entity creation

        $img = new Image();
        $img->setUrl($imgName);
        $img->setAlt( $observation->getImage()->getAlt());
        $img->setAuthor($this->getUser());

        $bird = $em->getRepository(Bird::class)->findOneBy(['name' => $observation->getBird()->getName()]);
        $bird->addImage($img);

        // NEED DO UPLOAD IMG WITH AUTHOR HERE

         $em->persist($bird);
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
