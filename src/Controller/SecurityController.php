<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Form\UserType;

class SecurityController extends Controller
{
     /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }
     /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request, AuthenticationUtils $authUtils)
    {
        return $this->render('index.html.twig');
    }


    /**
    * @Route("/signin", name="signin")
    */
      public function signinAction (Request $request, UserPasswordEncoderInterface $encoder) {

      $user = new User();
      $form = $this->createForm(UserType::class, $user);

      $user->setSalt(substr(base64_encode(random_bytes(23)),0,23)); //generer un sel aléatoire
      $user->setRoles(['ROLE_USER']);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {

        $encoded = $encoder->encodePassword($user, $user->getPassword() );

        $user->setPassword($encoded);

        $img = $user->getAvatar();
        // Generate a unique name for the file before saving it
        $imgName = md5(uniqid()).'.'.$img->guessExtension();
        // Move the file to the directory where brochures are stored
        $img->move(
          $this->getParameter('avatar_directory'),$imgName
        );
        // Update the 'brochure' property to store the PDF file name
        // instead of its contents
        $user->setAvatar($imgName);


        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->addFlash(
          'notice',
          'un utilisateur a été ajouté '
        );

        return $this->redirect('/');
      }

      return $this->render('security/signin.html.twig',[
        'form' => $form->createView()
      ]);


    }
}
