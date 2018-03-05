<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Form\UserType;
use App\Form\ResetPasswordType;

use App\Services\Mailer;

class SecurityController extends Controller
{
     /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils, $ajax = false)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        if($request->isXmlHttpRequest())
        {
            return $this->json(
                [
                    "state" => false,
                    "view" => $this->renderView('security/login.html.twig', array(
                                    'last_username' => $lastUsername,
                                    'error'         => $error,
                                    'ajaxLogin'     => true,
                                        )
                            )]);
        }


        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }
     /**
     * @Route("/resetPassword", name="resetPassword")
     */
    public function resetAction(Request $request, Mailer $mailer)
    {
      $email = $request->request->get('email');
          if (!is_null($email) ){

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

            $user->setPasswordRequestedAt(new \DateTime('now') );
            $user->setConfirmationToken(md5(uniqid())); //génération d'un token unique

            $em->persist($user);
            $em->flush();

            $mailer->sendResetPassword($user);


            $this->addFlash(
              'notice',
              'Un email vous a été envoyé'
            );

            return $this->redirectToRoute('homepage');
          }

      return $this->render('security/resetPassword.html.twig');
    }

    /**
    * @Route("/newPassword/{token}", name="newPassword")
    */
   public function newPasswordAction($token, Request $request, UserPasswordEncoderInterface $encoder)
   {
     $em = $this->getDoctrine()->getManager();
     $user = $em->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);
     $now = new \DateTime('now');
     $diff = $now->getTimestamp() - $user->getPasswordRequestedAt()->getTimestamp() ;
     if ( $diff > 86400) // nmbr de seconde dans 24h
     {

       $this->addFlash(
         'alert',
         'Votre token a expiré'
       );

       return $this->redirectToRoute('homepage');
     }

     $form = $this->createForm(ResetPasswordType::class, $user);

     $form->handleRequest($request);

     if ($form->isSubmitted() && $form->isValid()) {

         $encoded = $encoder->encodePassword($user, $user->getPassword() );
         $user->setPassword($encoded);

         $user->setConfirmationToken(null);
         $user->setPasswordRequestedAt(null);// on reset la réinitialisation

         $em->persist($user);
         $em->flush();

         $this->addFlash(
           'notice',
           'Votre Mot de passe a été mis à jour '
         );

         return $this->redirectToRoute('homepage');
       }
     return $this->render('security/newPassword.html.twig',[
       'form' => $form->createView()
     ]);
   }


    /**
    * @Route("/logout", name="logout")
    */
   public function logoutAction()
   {

   }



    /**
    * @Route("/signin", name="signin")
    */
      public function signinAction ( Request $request, UserPasswordEncoderInterface $encoder, Mailer $mailer) {

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

        $mailer->sendNewUser($user);

        $this->addFlash(
          'notice',
          'un utilisateur a été ajouté '
        );
        if($request->isXmlHttpRequest())
        {
          return $this->json(
                [
                    "state" => true
                ]
                );
        }
        return $this->redirect('/');
      }
      if($request->isXmlHttpRequest())
        {
          return $this->json([
              "state"=>false,
              "view" => $this->renderView('security/signin.html.twig',[
                    'form' => $form->createView(),
                    'ajaxLogin'     => true
                  ])
          ]);
        }
      return $this->render('security/signin.html.twig',[
        'form' => $form->createView()
      ]);
    }
    /**
     * @Route("/ajax-login-success", name="ajaxLoginSuccess")
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     */
    public function ajaxLoginSuccess(Request $request){

        return $this->json(
                [
                    "state" => true,
                    "view" => $this->renderView("security/loginSuccessModal.html.twig"),
                    "profil" => $this->renderView("security/profil.html.twig"),
                ]
                );
    }
}
