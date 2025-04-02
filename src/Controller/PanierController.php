<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Utilisateur;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Service\UtilsService;


#[Route('/panier', name: 'panier')]
final class PanierController extends AbstractController
{

    #[Route('/gerer_panier', name: '_gerer_panier')]
    #[isGranted('IS_AUTHENTICATED_FULLY')]
   public function gererPanierAction(UtilsService $panierService,Request $request): Response
   {
       $panier = $panierService->panierDeUtilisateur($this->getUser(),$request);
       $args = ['panier' => $panier];
       return $this->render('Panier/gerer_panier.html.twig', $args);
   }

   #[Route('/vider_panier', name: '_vider_panier')]
   #[isGranted('IS_AUTHENTICATED_FULLY')]
   public function viderPanierAction( UtilsService $panierService,Request $request):Response
   {
       $em = $panierService->get_entity_manager();
       $panier = $panierService->panierDeUtilisateur($this->getUser(),$request);
       if($panier != null){
           foreach ($panier as $prod){
               $produit = $prod->getProduit();
               $produit->setQuantiteEnStock($produit->getQuantiteEnStock() + $prod->getQuantite());
               $em->persist($produit);
               $em->remove($prod);

           }
           $this->addFlash('info','panier vidé avec succes');
       }else{
           $this->addFlash('info','deja panier vide');
       }
       $em->flush();

       return $this->redirectToRoute('panier_gerer_panier',['panier' =>$panier]);
   }


    #[Route('/commander', name: '_commander')]
    #[isGranted('IS_AUTHENTICATED_FULLY')]
    public function commanderAction(UtilsService $panierService,Request $request):Response
    {
        $em = $panierService->get_entity_manager();

        $panier = $panierService->panierDeUtilisateur($this->getUser(),$request);
        if($panier != null){
            foreach ($panier as $prod){
                $em->remove($prod);
            }
            $this->addFlash('info','Commande réalisé avec succes');
        }else{
            $this->addFlash('info','Rien a commander');
        }
        $em->flush();

        return $this->redirectToRoute('panier_gerer_panier',['panier' =>$panier]);
    }



    #[Route('/vider_produit_du_panier/{id_produit}',name: '_vider_produit_du_panier')]
    public function viderProduitDuPanier(UtilsService $utilsService,Request $request,int $id_produit):Response
    {
        $em= $utilsService->get_entity_manager();
        $panier_prod= $utilsService->panierDeUtilisateur($this->getUser(),$request);

        if($panier_prod != null){
            foreach ($panier_prod as $prod){
                $verif= false;
                if($verif == false){
                    if($prod->getId() == $id_produit)
                    {
                        $verif=true;
                        $produit = $prod->getProduit();
                        $em->remove($prod);

                        $produit->setQuantiteEnStock($produit->getQuantiteEnStock() + $prod->getQuantite());
                        $em->persist($produit);
                        $em->remove($prod);

                    }
                }else{
                    $this->addFlash('info','Produit retiré du panier avec succes');
                    $this->redirectToRoute('panier_gerer_panier',['panier' =>$panier_prod]);
                }
            }
        }else{
            $this->addFlash('info','Panier vide');

        }


        $em->flush();

        return $this->redirectToRoute('panier_gerer_panier',['panier' =>$panier_prod]);


    }


}
