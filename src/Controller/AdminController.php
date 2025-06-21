<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Service\UtilsService;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'admin')]
final class AdminController extends AbstractController
{
    #[Route('/gerer_clients', name: '_gerer_clients')]
    public function gererClientsAction(UtilsService $utilsService): Response
    {
        $utilisateurs = $utilsService->get_utilisateurs_find_all();
        $args = ['utilisateurs' => $utilisateurs];
        return $this->render('Admin/gerer_clients.html.twig', $args);
    }

    #[Route('/gerer_produits', name: '_gerer_produits')]
    public function gererProduitsAction(UtilsService $utilsService): Response
    {
        $args = ['produits' => $utilsService->get_produits_find_all()];
        return $this->render('Admin/gerer_produits.html.twig', $args);
    }
}
