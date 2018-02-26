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
use Knp\Component\Pager\Paginator;

class AppController extends Controller {

    /**
     * @Route("/", name="homepage")
     */
    public function index() {
        // replace this line with your own code!
        return $this->render("base.html.twig");
    }

    /**
     * @Route("/user/{username}", name="userpage")
     */
    public function userpage($username) {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);


        if (!$user) {
            $this->get('session')->getFlashBag()->add('alert', 'Il n\'y a pas d\'utilisateur à ce nom');
            return $this->redirectToRoute('homepage');
        } else {
            $observations = $em->getRepository(Observation::class)->findBy(['user' => $user->getId()]);
            return $this->render("userpage.html.twig", array(
                        'user' => $user,
                        'observations' => $observations));
        }
    }


    /**
     * @Route("/bird/change/{birdId}", name="birdDescription")
     */
    public function birdDescriptionAction($birdId, Request $request)
    {
    $em = $this->getDoctrine()->getManager();
    $bird = $em->getRepository(Bird::class)->find($birdId);

    
    $description= $request->request->get('description');
    $bird->setDescription($description);
    $bird->setDescriptionValid(false);
    $bird->setContributor($this->getUser()->getUsername());

    $em->persist($bird);
    $em->flush();

        return $this->redirectToRoute("birdpage", array(
            'slug'=> $bird->getSlug(),
            'bird'=> $bird
        ));

    }

     /**
     * @Route("/oiseaux", name="birds")
     */
    public function birdsPage( /*Paginator $paginator, */ Request $request) {

        $em = $this->getDoctrine()->getManager();
        $birds = $em->getRepository(Bird::class)->findAll();
       // $qb = $repository->createQueryBuilder('b');

       $birdslist  = $this->get('knp_paginator')->paginate(
        $birds,
        $request->query->get('page', 1)/*le numéro de la page à afficher*/,
          9/*nbre d'éléments par page*/
    );

        if (!$birds) {
            $this->get('session')->getFlashBag()->add('alert', 'Il n\'y a pas d\'oiseaux');
            return $this->render("birds.html.twig");
        } else {
            return $this->render("birds.html.twig", array(
                        'birds' => $birds,
                        'birdslist' => $birdslist));
        }
    }

    /**
     * @Route("/bird/{slug}", name="birdpage")
     */
    public function birdPage($slug) {
        $em = $this->getDoctrine()->getManager();
        $bird = $em->getRepository(Bird::class)->findOneBy(['slug' => $slug]);
        $observations = $em->getRepository(Observation::class)->findBy(['bird' => $bird]);


        if (!$bird) {
            $this->get('session')->getFlashBag()->add('alert', 'Il n\'y a pas d\'utilisateur à ce nom');
            return $this->render("birdpage.html.twig");
        } else {
            return $this->render("birdpage.html.twig", array(
                        'bird' => $bird,
                        'obs'=> $observations ));
        }
    }

    /**
     * @Route("/carte", name="map")
     */
    public function application(Request $request) {
        return $this->render("map.html.twig");
    }

    /**
     * @Route("/get-bird-list", name="bird-search")
     */
    public function birdSearch(Request $request) {
        $query = $request->query->get("term");
        $em = $this->getDoctrine()->getManager();
        $birdsList = $em->getRepository(Bird::class)->search($query);
        $birdJson = [];
        foreach ($birdsList as $bird) {
            $birdJson[] = [
                "birdId" => $bird->getId(),
                "birdName" => $bird->getName(),
                "birdLatinName" => $bird->getLatinName(),
                "birdSlug" => $bird->getSlug(),
            ];
        }
        return $this->json($birdJson);
    }

    /**
     * 
     * @Route("/get-observations", name="getObservations")
     */
    public function getObservations(Request $request, \Doctrine\ORM\EntityManagerInterface $em) {

        $observations = $em->getRepository(Observation::class)->findByBird($request->query->get("bird"));

        if(count($observations)<=0 && $this->getParameter('fake_data')){
            $observations = $this->get("data_faker")->getfakeObservations($request->query->get("bird"));
        }
        
        return $this->json(
                        array_map(function($obs) {
                            
                            $img = $obs->getImage();
                            return [
                                "lat" => $obs->getGeoloc("lat"),
                                "lng" => $obs->getGeoloc("lng"),
                                "thumbnail" => (!is_null($img))?$img->getUrl():"",
                                "url" => (!is_null($img))?$img->getUrl():"",
                                "caption" => $obs->getDescription()
                            ];
                        }, $observations)
        );
    }

    /**
     * @Route("/post", name="post")
     */
    public function postObservation(Request $request) {

        $observation = new Observation();
        $form = $this->createForm(ObservationType::class, $observation);

        $observation->setUser($this->getUser());
        $observation->setValid(FALSE);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $img = $observation->getImage()->getUrl();
            // Generate a unique name for the file before saving it
            $imgName = md5(uniqid()) . '.' . $img->guessExtension();
            $img->move(
                    $this->getParameter('post_directory'), $imgName
            );
            $observation->getImage()->setUrl($imgName);
            $observation->SetValid(FALSE);
            $observation->setDescription($observation->getImage()->getAlt());


            $em = $this->getDoctrine()->getManager();
            $em->persist($observation);
            $em->flush();

            // IMAGE Entity creation

            $img = new Image();
            $img->setUrl($imgName);
            $img->setAlt($observation->getImage()->getAlt());
            $img->setAuthor($this->getUser());

            $bird = $em->getRepository(Bird::class)->findOneBy(['name' => $observation->getBird()->getName()]);
            $bird->addImage($img);

            // NEED DO UPLOAD IMG WITH AUTHOR HERE

            $em->persist($bird);
            $em->flush();

            $this->addFlash(
                    'notice', 'une observation a été ajouté '
            );

            return $this->redirect('/');
        }

        return $this->render('post.html.twig', [
                    'form' => $form->createView()
        ]);
    }

 
}
