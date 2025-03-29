<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/accessuser', name: '_accessuser')]
    public function accessUserAction(): Response
    {
        return $this->render('Security/access_user.html.twig');
    }


    #[Route('/test', name: '_test')]
    //#[IsGranted(’ROLE_SALARIE’, statusCode: Response::HTTP_NOT_FOUND, message: ’No access! Get out!’)]
    //#[IsGranted(new Expression(’is_granted(\’ROLE_SALARIE\’) or is_granted("ROLE_GESTION")’))]
    #[isGranted('ROLE_ADMIN')]
    public function testAction(): Response{
        return new Response('test');
    }

}
