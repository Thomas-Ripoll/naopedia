<?php

namespace App\Controller;


use App\Entity\Bird;
use App\Entity\Image;
use App\Entity\Observation;
use App\Entity\User;
use App\Entity\Article;
use App\Form\ObservationType;
use App\Services\QueryStringDecoder;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class BlogControlleController extends Controller
{
    
    /**
    * @Route("/articles", name="articles")
    */
    public function listArticlePage( Request $request) {
        
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository(Article::class)->findAll();
        // $qb = $repository->createQueryBuilder('b');
        
        $articleslist  = $this->get('knp_paginator')->paginate(
            $articles,
            $request->query->get('page', 1)/*le numéro de la page à afficher*/,
            9/*nbre d'éléments par page*/
        );
        
        if (!$articles) {
            $this->get('session')->getFlashBag()->add('alert', 'Il n\'y a pas d\'articles');
            return $this->redirectToRoute('homepage');
        } else {
            foreach ($articles as $article) {
                if ($article->getCategory()==['trend']) //category is an array
                $trendArticle = $article;
            }
            if(isset($trendArticle)){
                return $this->render("listArticles.html.twig", array(
                    'trendArticle' => $trendArticle,
                    'articles' => $articles,
                    'articleslist' => $articleslist));
                }
                return $this->render("listArticles.html.twig", array(
                    'articles' => $articles,
                    'articleslist' => $articleslist));
                }
            }
            
            
            /**
            * @Route("/article/{id}", name="article")
            */
            public function ArticlePage($id, Request $request) {
                
                $em = $this->getDoctrine()->getManager();
                $article = $em->getRepository(Article::class)->find($id);
                
                if (!$article) {
                    $this->get('session')->getFlashBag()->add('alert', 'Il n\'y a pas d\'article à cet ID');
                    return $this->redirectToRoute('homepage');
                }
                else {
                    return $this->render("article.html.twig", array(
                        'article' => $article));
                        
                    }
                }
            }
            