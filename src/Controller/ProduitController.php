<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Form\PanierType;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\FormFactoryInterface;

use App\Service\UtilsService;



#[Route('/produit', name: 'produit')]
final class ProduitController extends AbstractController
{
    #[Route('/liste_produits', name: '_liste_produits')]
    public function listeProduitsAction(FormFactoryInterface $formFactory, UtilsService $panierService, Request $request): Response
    {
        $em = $panierService->get_entity_manager();

        $formulaires = [];
        $formulaires_vue = [];

        $produits = $em->getRepository(Produit::class)->findAll();
        $prodList = [];

        foreach ($produits as $produit) {
            $quantiteMin=0;
            $prodList[$produit->getId()] = $produit;

            $panier = new Panier();
            //$form = $this->createForm(PanierType::class, $panier, ['quantite_max' => $produit->getQuantiteEnStock()]);

            $panier_Courant = $panierService->retourPanierUtilisateurAction($request,$this->getUser(),$produit);
            if($panier_Courant != null){
                $quantiteMin = $panier_Courant->getQuantite();

            }


            $form = $formFactory->createNamed('panier_'.$produit->getId(), PanierType::class, $panier, [
                'quantite_max' => $produit->getQuantiteEnStock(),
                'quantite_min' => $quantiteMin-2*$quantiteMin,
            ]);

            $form->add('valider', SubmitType::class, ['label' => 'Modifier']);

            $formulaires[$produit->getId()] = $form;
            $formulaires_vue[$produit->getId()] = $form->createView();
        }

        $formTraite = false;

        foreach ($formulaires as $productId=>$form) {

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $quantiteTemp = $form->get('quantite')->getData();
                $prod = $prodList[$productId];

               /* if($quantiteTemp<1){
                    $this->addFlash('info','quantite incorrect');
                }else{*/

                    $retour = $this->utilisateurEtProduitDansPanier($this->getUser(), $prod,$panierService);
                    if ($retour==null) {
                        $panier = $form->getData();
                        $panier->setUtilisateur($this->getUser());
                        $panier->setProduit($prod);
                        $panier->setQuantite($quantiteTemp);

                        $em->persist($panier);
                        $em->flush();

                        $this->retirerQuantiteProduit($prod,$quantiteTemp,$panierService);

                        $this->addFlash('info','panier modifié avec succes');
                        $formTraite = true;
                    }else{
                        $this->addFlash('info',$prod->getNom().' est deja dans votre panier, mise a jour de la quantite');
                        $retour->setQuantite($retour->getQuantite() + $quantiteTemp);
                        $em->persist($retour);
                        $em->flush();

                        $this->retirerQuantiteProduit($prod,$quantiteTemp,$panierService);

                        $formTraite = true;
                    }

                /*}*/


            }elseif ( $form->isSubmitted() && !$form->isValid()){
                $this->addFlash('info','formulaire de creation de panier incorrect');
            }
        }
        if($formTraite){
            return $this->redirectToRoute('produit_liste_produits');
        }

        //je cherche tt les paniers de l'utilisateur courrant
        $panier_utilisateur = $panierService->panierDeUtilisateur($this->getUser(),$request);


        $args = [
            'produits' => $produits,
            'formulaires' => $formulaires_vue,
            'panier_utilisateur' => $panier_utilisateur,
        ];

        return $this->render('Produit/liste_produits.html.twig', $args);
    }



    #[Route('/ajouter_produit', name: '_ajouter_produit')]
    #[IsGranted('ROLE_ADMIN')]
    public function ajouterProduitAction(UtilsService $utilsService,Request $request): Response
    {
        $em = $utilsService->get_entity_manager();

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


/*
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
*/


    #[Route('/afficher_produit/{id_produit}', name: '_afficher_produit')]
    public function afficherProduitAction($id_produit,UtilsService $utilsService): Response
    {
        $em = $utilsService->get_entity_manager();
        $produit = $em->getRepository(Produit::class)->find($id_produit);
        if (!$produit) {
            $this->addFlash('info', 'Produit non trouve');
            return $this->redirectToRoute('produit_liste_produits');
        }
        $args = ['produit' => $produit];
        return $this->render('Produit/afficher_produit.html.twig', $args);
    }

    public function utilisateurEtProduitDansPanier(Utilisateur $utilisateur,Produit $produit,UtilsService $utilsService): ?Panier
    {
        $manager = $utilsService->get_entity_manager();
        $panierRepository = $manager->getRepository(Panier::class);
        $queryBuilder = $panierRepository->createQueryBuilder('panier');
        $queryBuilder->where('panier.utilisateur = :utilisateur')
            ->andWhere('panier.produit = :produit')
            ->setParameter('utilisateur', $utilisateur)
            ->setParameter('produit', $produit);
        $query = $queryBuilder->getQuery()->getResult();
        if($query == null){
            return null;
        }else{
            return $query[0];
        }
    }

    public function retirerQuantiteProduit(Produit $produit,int $quantite,UtilsService $utilsService){
        $manager = $utilsService->get_entity_manager();
        $resultat = $produit->getQuantiteEnStock() - $quantite;
        if($resultat>=0){
            $produit->setQuantiteEnStock($produit->getQuantiteEnStock() - $quantite);
            $manager->persist($produit);
            $manager->flush();
        }else{
            $this->addFlash('info','cela fait tomber la quantite du produit < 0');
        }
    }

}
