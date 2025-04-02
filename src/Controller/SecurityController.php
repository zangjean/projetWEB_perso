<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;

#[Route(path: '/security', name: 'security')]
class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: '_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('Security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: '_logout')]
    public function logout(): void
    {
        $user = $this->getUser();
        dump($user);
        $this->addFlash('info', 'Vous etes à présent déconnecté');
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(name: '_after_logout')] //cette route n'est utilisé que par security
    public function afterLogout(Request $request): Response
    {
        $request->getSession()->set('just_logged_out', true);
        return $this->redirectToRoute('acceuil');
    }

}
