<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Form\PanierType;
use App\Form\ProduitType;
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
            $panier_Courant = $panierService->retourPanierUtilisateurAction($this->getUser(),$produit);
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
        $traitementEffectue = false;
        foreach ($formulaires as $productId=>$form) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $quantiteDemandee = $form->get('quantite')->getData();
                $prod = $prodList[$productId];
                    $panierExist = $panierService->retourPanierUtilisateurAction($this->getUser(),$prod);
                    if ($panierExist==null) {
                        $panier = $form->getData();
                        $panier->setUtilisateur($this->getUser());
                        $panier->setProduit($prod);
                        $panier->setQuantite($quantiteDemandee);
                        $em->persist($panier);
                        $em->flush();
                        $panierService->retirerQuantiteProduit($prod,$quantiteDemandee);
                        $this->addFlash('info','panier modifié avec succes');
                        $traitementEffectue = true;
                    }else{
                        $this->addFlash('info',$prod->getNom().' est deja dans votre panier, mise a jour de la quantite');
                        $panierExist->setQuantite($panierExist->getQuantite() + $quantiteDemandee);
                        $em->persist($panierExist);
                        $em->flush();
                        $panierService->retirerQuantiteProduit($prod,$quantiteDemandee);
                        $traitementEffectue = true;
                    }
            }elseif ( $form->isSubmitted() && !$form->isValid()){
                $this->addFlash('info','formulaire de creation de panier incorrect');}}
        if($traitementEffectue){
            return $this->redirectToRoute('produit_liste_produits');
        }
        $panier_utilisateur = $panierService->panierDeUtilisateur($this->getUser());
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
        $args = array('form_ajouter_produit' => $form );
        return $this->render('Produit/ajouter_produit.html.twig',$args);
    }

    #[Route('/afficher_produit/{id_produit}', name: '_afficher_produit',requirements: ['id_produit' => '\d+'])]
    public function afficherProduitAction(int $id_produit,UtilsService $utilsService): Response
    {
        $produit = $utilsService->get_produitRepository()->find($id_produit);
        if (!$produit) {
            $this->addFlash('info', 'Produit non trouve');
            return $this->redirectToRoute('produit_liste_produits');
        }
        $args = ['produit' => $produit];
        return $this->render('Produit/afficher_produit.html.twig', $args);
    }
}
