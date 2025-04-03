<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Service\UtilsService;

#[Route('/admin', name: 'admin')]
final class AdminController extends AbstractController
{
    #[Route('/gerer_clients', name: '_gerer_clients')]
    public function gererClientsAction(UtilsService $utilsService): Response
    {
        $utilisateurs = $utilsService->get_entity_manager()->getRepository(Utilisateur::class);
        $args = ['utilisateurs' => $utilisateurs->findAll()];
        return $this->render('Admin/gerer_clients.html.twig', $args);
    }


    #[Route('/supprimer_client/{id_client}',
        name: '_supprimer_client'
    )]
    public function supprimerClientAction($id_client,UtilsService $utilsService): Response
    {
        $em = $utilsService->get_entity_manager();
        if($id_client == $this->getUser())
        {
            $this->addFlash('info','Vous ne pouvez pas supprimer votre compte');
        }else{
            $client = $em->getRepository(Utilisateur::class)->find($id_client);
            if($client){
                $em->remove($client);
                $em->flush();
                $this->addFlash('info','Client supprimer avec succes');
            }else{
                $this->addFlash('info','Client non trouve');
            }
        }
        return $this->redirectToRoute('admin_gerer_clients');

    }


    #[Route('/gerer_produits', name: '_gerer_produits')]
    public function gererProduitsAction(UtilsService $utilsService): Response
    {
        $em=$utilsService->get_entity_manager();
        $produits = $em->getRepository(Produit::class)->findAll();
        $args = ['produits' => $produits];
        return $this->render('Admin/gerer_produits.html.twig', $args);
    }
}
