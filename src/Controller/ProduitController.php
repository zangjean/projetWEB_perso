<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/produit', name: 'produit')]
final class ProduitController extends AbstractController
{
    #[Route('/liste_produits', name: '_liste_produits')]
    public function listeProduitsAction(EntityManagerInterface $em): Response
    {
        $produits = $em->getRepository(Produit::class)->findAll();
        $args = ['produits' => $produits];

        return $this->render('Produit/liste_produits.html.twig', $args);
    }



    #[Route('/ajouter_produit', name: '_ajouter_produit')]
    #[IsGranted('ROLE_ADMIN')]
    public function ajouterProduitAction(EntityManagerInterface $em, Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->add('valider', SubmitType::class,['label' => 'Valider']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($produit);
            $em->flush();
            $this->addFlash('info','Produit creer avec succes');
            return $this->redirectToRoute('admin_gerer_produits');
        }else{
            $this->addFlash('info','formulaire de creation de produit incorrect');
        }

        $args = array(
            'form_ajouter_produit' => $form
        );
        return $this->render('Produit/ajouter_produit.html.twig',$args);

    }



    #[Route('/supprimer_produit/{id_produit}', name: '_supprimer_produit')]
    #[IsGranted('ROLE_ADMIN')]
    public function supprimerProduitAction($id_produit,EntityManagerInterface $em): Response
    {
        $produit = $em->getRepository(Produit::class)->find($id_produit);
        if ($produit) {
            $em->remove($produit);
            $em->flush();
            $this->addFlash('info', 'Produit supprimer avec succes');
        } else {
            $this->addFlash('info', 'Produit non trouve');
        }
        return $this->redirectToRoute('admin_gerer_produits');
    }



    #[Route('/afficher_produit/{id_produit}', name: '_afficher_produit')]
    public function afficherProduitAction($id_produit,EntityManagerInterface $em): Response
    {
        $produit = $em->getRepository(Produit::class)->find($id_produit);
        if (!$produit) {
            $this->addFlash('info', 'Produit non trouve');
            return $this->redirectToRoute('produit_liste_produits');
        }
        $args = ['produit' => $produit];
        return $this->render('Produit/afficher_produit.html.twig', $args);
    }
}
