<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/connexion', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
                 // get the login error if there is one
                 $error = $authenticationUtils->getLastAuthenticationError();

                 // last username entered by the user
                 $lastUsername = $authenticationUtils->getLastUsername();

          return $this->render('login/index.html.twig', [
                           'last_username' => $lastUsername,
                           'controller_name'=> 'yehouda',
                           'error'         => $error,
          ]);
      }
    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/redirect', name: 'app_redirect',methods: ['GET'])]
    public function redirectsection(UserInterface $user)
    {
        if ($this->getUser()) {
            $user = $this->getUser();
            switch ($user->getRoles()[0]) {
                case "ROLE_USER":
                    return $this->redirectToRoute('app_home');
            }
        }
        return $this->redirectToRoute('app_login');
    }
}
