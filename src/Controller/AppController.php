<?php

namespace App\Controller;

use App\Entity\Bird;
use App\Entity\Image;
use App\Entity\Observation;
use App\Entity\User;
use App\Form\ObservationType;
use App\Services\QueryStringDecoder;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AppController extends Controller {

    /**
     * @Route("/", name="homepage")
     */
    public function index(EntityManagerInterface $em) {

        $articlesRep = $em->getRepository(\App\Entity\Article::class);
        $lastObservations = $em->getRepository(\App\Entity\Observation::class)->findBy([], ["date" => "DESC"], 3);
        $lastArticles = $articlesRep->findBy([], ["date" => "DESC"], 2);

        $artMisenAvant = $articlesRep->findByCategory('trend');
        $artDoc = $articlesRep->findByCategory('documentation');

        return $this->render("base.html.twig", [
                    "artMisenAvant" => $artMisenAvant,
                    "artDoc" => $artDoc,
                    "lastArticles" => $lastArticles,
                    "lastObservations" => $lastObservations
        ]);
    }

    /**
     * @Route("/user/{username}", name="userpage")
     * @ParamConverter("user", options={"mapping": {"username": "username"}})
     */
    public function userpage(User $user, UploaderHelper $vichHelper) {
        $datesArray = [];
        $obsValid = [];

        foreach ($user->getObservations() as $obs){ // On affiche que les observation validées 
            if($obs->getValid() == false){
                $obsValid[] = $obs;
            }
        }
        foreach ($obsValid as $obs) {
            $img = $obs->getImage();
            if (!key_exists($obs->getSearchDate(), $datesArray)) {
                $datesArray[$obs->getSearchDate()] = [];
            }
            $datesArray[$obs->getSearchDate()][] = [
                "id" => $obs->getId(),
                "lat" => $obs->getGeoloc("lat"),
                "lng" => $obs->getGeoloc("lng"),
                "url" => (!is_null($img)) ? $img->getUrl() : "",
                "caption" => $obs->getDescription(),
                "author" => $obs->getUser()->getUsername(),
                "day" => $obs->getDate()->format("j"),
                "date" => $obs->getDate()->format("d/m/Y"),
                "birdName" => $obs->getBird()->getLatinName(),
                "birdSlug" => $obs->getBird()->getSlug(),
                "img" => (!is_null($img)) ? [
            "id" => $img->getId(),
            "url" => (preg_match("/http/", $img->getUrl())) ? $img->getUrl() : $vichHelper->asset($img, "imageFile"),
            "liked" => !is_null($user) && in_array($user->getId(), $img->getLikes()),
            "countLikes" => count($img->getLikes())
                ] : null
            ];
        }
        $dataArray = [
            "data" => ["all" => $datesArray]
        ];
        return $this->render("userpage.html.twig", array(
                    'user' => $user,
                    'mapData' =>  $dataArray));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/bird/change/{birdId}", name="birdDescription")
     */
    public function birdDescriptionAction($birdId, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $bird = $em->getRepository(Bird::class)->find($birdId);


        $description = $request->request->get('description');
        $bird->setDescription($description);
        $bird->setDescriptionValid(false);
        $bird->setContributor($this->getUser());

        $em->persist($bird);
        $em->flush();


        return $this->json(array(
                    'state' => 'success'
        ));
    }

    /**
     * @Route("/oiseaux", name="birds")
     */
    public function birdsPage(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $birds = $em->getRepository(Bird::class)->findAll();
        // $qb = $repository->createQueryBuilder('b');

        $birdslist = $this->get('knp_paginator')->paginate(
                $birds, $request->query->get('page', 1)/* le numéro de la page à afficher */, 9/* nbre d'éléments par page */
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
     * @Route("/oiseau/{slug}", name="birdpage")
     */
    public function birdPage($slug) {
        $em = $this->getDoctrine()->getManager();
        $bird = $em->getRepository(Bird::class)->findOneBy(['slug' => $slug]);
        $observations = $em->getRepository(Observation::class)->findBy(['bird' => $bird]);


        if (!$bird) {
            $this->get('session')->getFlashBag()->add('alert', 'Il n\'y a pas d\'oiseau à ce nom');

            return $this->render("birdpage.html.twig");
        } else {
            return $this->render("birdpage.html.twig", array(
                        'bird' => $bird,
                        'obs' => $observations));
        }
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
     * @Route("/carte", name="map")
     */
    public function application(Request $request, EntityManagerInterface $em, QueryStringDecoder $qsd, UploaderHelper $vichHelper) {
        $parameters = $qsd->decodeUrl($request->query);

        $observations = $em->getRepository(Observation::class)->findByFilters($parameters["query"]);

        $datesArray = [];
        $user = $this->getUser();
        foreach ($observations as $obs) {
            $img = $obs->getImage();
            if (!key_exists($obs->getSearchDate(), $datesArray)) {
                $datesArray[$obs->getSearchDate()] = [];
            }
            $datesArray[$obs->getSearchDate()][] = [
                "id" => $obs->getId(),
                "lat" => $obs->getGeoloc("lat"),
                "lng" => $obs->getGeoloc("lng"),
                "url" => (!is_null($img)) ? $img->getUrl() : "",
                "caption" => $obs->getDescription(),
                "author" => $obs->getUser()->getUsername(),
                "day" => $obs->getDate()->format("j"),
                "date" => $obs->getDate()->format("d/m/Y"),
                "birdName" => $obs->getBird()->getLatinName(),
                "birdSlug" => $obs->getBird()->getSlug(),
                "img" => (!is_null($img)) ? [
            "id" => $img->getId(),
            "url" => (preg_match("/http/", $img->getUrl())) ? $img->getUrl() : $vichHelper->asset($img, "imageFile"),
            "liked" => !is_null($user) && in_array($user->getId(), $img->getLikes()),
            "countLikes" => count($img->getLikes())
                ] : null
            ];
        }
        if (key_exists("dates", $parameters["query"]) && count($parameters["query"]["dates"]) > 0) {
            foreach ($parameters["query"]["dates"] as $date) {
                if (!key_exists($date, $datesArray)) {
                    $datesArray[$date] = [];
                }
            }
        }
        $bird = ($request->query->has("bird")) ? $parameters["query"]["bird"] : "all";
        $dataArray = [];
        $dataArray = [
            "data" => [$bird => $datesArray],
            "filters" => $parameters["filters"]
        ];

        $observation = new Observation();
        $form = $this->createForm(ObservationType::class, $observation, ["action" => $this->generateUrl("post")]);

        return $this->render("map.html.twig", ["birdsloaded" => $dataArray, "form" => $form->createView()]);
    }

    /**
     *
     * @Route("/get-observations", name="getObservations")
     */
    public function getObservations(Request $request, EntityManagerInterface $em, QueryStringDecoder $qsd, UploaderHelper $vichHelper) {

        $query = $qsd->decode($request->query);
        //dump($query);
        $observations = $em->getRepository(Observation::class)->findByFilters($query);

        if (count($observations) <= 0 && $this->getParameter('fake_data') && $request->query->has("bird")) {

            $this->get("data_faker")->getfakeObservations($request->query->get("bird"));
            $observations = $em->getRepository(Observation::class)->findByFilters($query);
        }
        //$observations = [];
        /* foreach($em->getRepository(Observation::class)->findAll() as $obs){
          $obs->setDate($obs->getDate());
          $em->persist($obs);
          }
          $em->flush(); */
        $datesArray = [];
        $user = $this->getUser();
        foreach ($observations as $obs) {
            $img = $obs->getImage();
            if (!key_exists($obs->getSearchDate(), $datesArray)) {
                $datesArray[$obs->getSearchDate()] = [];
            }
            $datesArray[$obs->getSearchDate()][] = [
                "id" => $obs->getId(),
                "lat" => $obs->getGeoloc("lat"),
                "lng" => $obs->getGeoloc("lng"),
                "url" => (!is_null($img)) ? $img->getUrl() : "",
                "caption" => $obs->getDescription(),
                "author" => $obs->getUser()->getUsername(),
                "day" => $obs->getDate()->format("j"),
                "date" => $obs->getDate()->format("d/m/Y"),
                "birdName" => $obs->getBird()->getLatinName(),
                "birdSlug" => $obs->getBird()->getSlug(),
                "img" => (!is_null($img)) ? [
            "id" => $img->getId(),
            "url" => (preg_match("/http/", $img->getUrl())) ? $img->getUrl() : $vichHelper->asset($img, "imageFile"),
            "liked" => !is_null($user) && in_array($user->getId(), $img->getLikes()),
            "countLikes" => count($img->getLikes())
                ] : null
            ];
        }
        if ($request->query->has("dates")) {
            foreach ($query["dates"] as $date) {
                if (!key_exists($date, $datesArray)) {
                    $datesArray[$date] = [];
                }
            }
        }

        return $this->json($datesArray);
    }

    /**
     * @Route("/get-observation-image/{obs}")
     */
    public function getImage(Observation $obs) {

        $img = $obs->getImage();

        $user = $this->getUser();

        return $this->json([
                    "id" => $img->getId(),
                    "url" => $img->getUrl(),
                    "liked" => !is_null($user) && in_array($user->getId(), $img->getLikes()),
                    "countLikes" => count($img->getLikes())
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/like-image/{image}")
     *
     * @param Request $request
     */
    public function likeImage(Request $request, Image $image, EntityManagerInterface $em) {


        $likes = $image->getLikes();
        $user_id = $this->getUser()->getId();
        $isLiked = in_array($user_id, $likes);

        if ($isLiked) {
            $image->removeLike($user_id);
        } else {
            $image->addLike($user_id);
        }
        $em->persist($image);
        $em->flush();
        return $this->json([
                    "like" => !$isLiked,
                    "countLikes" => count($image->getLikes())
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/post", name="post")
     */
    public function postObservation(Request $request) {

        $observation = new Observation();
        $form = $this->createForm(ObservationType::class, $observation, ["action" => $this->generateUrl("post")]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($observation);
            $bird = $observation->getBird();
            if ($bird) {
                $bird->addImage($observation->getImage());
                $em->persist($bird);
            }
            $em->flush();


            $observation = new Observation();
            $form = $this->createForm(ObservationType::class, $observation, ["action" => $this->generateUrl("post")]);
            return $this->json([
                        "state" => "success",
                        "view" => $this->renderView("post-form.html.twig", ["form" => $form->createView()])
            ]);
        }

        return $this->json([
                    "state" => "failure",
                    "view" => $this->renderView("post-form.html.twig", ["form" => $form->createView()])
        ]);
    }

}
