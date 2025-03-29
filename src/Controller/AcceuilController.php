<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class AcceuilController extends AbstractController{


    #[Route('/', name: 'acceuil')]
    public function acceuilAction(): Response
    {
        $args = ['utilisateur' => $this->getUser()];
        return $this->render('Acceuil/acceuil.html.twig', $args);
    }
}
