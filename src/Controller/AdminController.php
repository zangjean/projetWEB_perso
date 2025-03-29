<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'admin')]
final class AdminController extends AbstractController
{

    #[Route('/gerer_clients', name: '_gerer_clients')]
    public function gererClientsAction(EntityManagerInterface $em): Response
    {
        $utilisateurs = $em->getRepository(Utilisateur::class);

        $qb = $utilisateurs->createQueryBuilder('client')
            ->where('client.roles LIKE :roles')
            ->setParameter('roles', '%ROLE_CLIENT%');
        $clients = $qb->getQuery()->getResult();

        $args = ['clients' => $clients];
        return $this->render('Admin/gerer_clients.html.twig', $args);

    }


    #[Route('/supprimer_client/{id_client}',
        name: '_supprimer_client'
    )]
    public function supprimerClientAction($id_client,EntityManagerInterface $em): Response
    {
        $client = $em->getRepository(Utilisateur::class)->find($id_client);
        if($client){
            $em->remove($client);
            $em->flush();
            $this->addFlash('info','Client supprimer avec succes');
        }else{
            $this->addFlash('info','Client non trouve');
        }
        return $this->redirectToRoute('admin_gerer_clients');

    }


    #[Route('/gerer_produits', name: '_gerer_produits')]
    public function gererProduitsAction(EntityManagerInterface $em): Response
    {
        $produits = $em->getRepository(Produit::class)->findAll();
        $args = ['produits' => $produits];
        return $this->render('Admin/gerer_produits.html.twig', $args);
    }
}
