<?php

namespace App\Controller;

use App\Service\UtilsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class AcceuilController extends AbstractController{


    #[Route('/', name: 'acceuil')]
    public function acceuilAction(Request $request): Response
    {
        if ($request->getSession()->get('just_logged_out')) {
            $this->addFlash('info', 'Vous avez été déconnecté.');
            $request->getSession()->remove('just_logged_out');
        }

        $args = ['utilisateur' => $this->getUser()];
        return $this->render('Acceuil/acceuil.html.twig', $args);
    }

    public function afficherQauntitePanierAction (UtilsService $utilsService,Request $request):Response
    {
        $quantitePanier =0;
        if($this->isGranted('ROLE_CLIENT')){
            $quantitePanier = $utilsService->quantitePanierUtil($this->getUser()->getId(),$request);
        }

        return $this->render('Panier/afficher_quantite_panier.html.twig', ['quantitePanier' => $quantitePanier]);
    }
}
