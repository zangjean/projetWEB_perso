<?php

namespace App\Service;

use App\Controller\UtilisateurController;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\User;
use App\Entity\Utilisateur;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;


class UtilsService
{
    private EntityManagerInterface $em;
    private PanierRepository $panierRepository;
    private UtilisateurRepository $utilisateurRepository;
    private ProduitRepository $produitRepository;

    private array $panier_find_all; //pour stocker le résultat du findAll et ne pas le refaire tt le temps
    private array $produits_find_all;
    private array $utilisateurs_find_all;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->em = $manager;

        $this->panierRepository = $this->em->getRepository(Panier::class);
        $this->utilisateurRepository = $this->em->getRepository(Utilisateur::class);
        $this->produitRepository = $this->em->getRepository(Produit::class);

        $this->panier_find_all = $this->panierRepository->findAll();
        $this->produits_find_all = $this->produitRepository->findAll();
        $this->utilisateurs_find_all = $this->utilisateurRepository->findAll();

    }

    public function get_entity_manager(): EntityManagerInterface
    {
        return $this->em;
    }

    public function get_panierRepository(): EntityRepository
    {
        return $this->panierRepository;
    }

    public function get_utilisateurRepository(): EntityRepository
    {
        return $this->utilisateurRepository;
    }

    public function get_produitRepository(): EntityRepository
    {
        return $this->produitRepository;
    }

    public function get_panier_find_all(): array
    {
        return $this->panier_find_all;
    }

    public function get_produits_find_all(): array
    {
        return $this->produits_find_all;
    }

    public function get_utilisateurs_find_all(): array
    {
        return $this->utilisateurs_find_all;
    }





    //renvoi tout le panier d'un utilisateur
    public function panierDeUtilisateur(Utilisateur $utilisateur):array
    {
        $queryBuilder_panier = $this->em->getRepository(Panier::class)->createQueryBuilder('panier')
            ->where('panier.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur);
        $panier = $queryBuilder_panier->getQuery()->getResult();

        return $panier;
    }

    //renvoi la ligne du panier correspondant au produit donné en paramètre si elle existe
    public function retourPanierUtilisateurAction(Utilisateur $utilisateur,Produit $produit): ?Panier
    {
        $queryBuilder_panier = $this->panierRepository->createQueryBuilder('panier')
            ->where('panier.utilisateur = :utilisateur')
            ->andWhere('panier.produit = :produit')
            ->setParameter('produit', $produit)
            ->setParameter('utilisateur', $utilisateur);

        $query = $queryBuilder_panier->getQuery()->getResult();
        if($query == null){
            return null;
        }else{
            return $query[0];
        }

    }

    //pour retourner la quantite de produit dans le panier de l'utilisateur
    public function quantitePanierUtil(int $id_utilisateur): int
    {
        $panier = $this->panierDeUtilisateur($this->utilisateurRepository->find($id_utilisateur));
        $quantite = 0;
        foreach ($panier as $prod){
            $quantite += $prod->getQuantite();
        }
        return $quantite;
    }

    public function retirerQuantiteProduit(Produit $produit,int $quantite){
        $manager = $this->em;
        $resultat = $produit->getQuantiteEnStock() - $quantite;
        if($resultat>=0){
            $produit->setQuantiteEnStock($produit->getQuantiteEnStock() - $quantite);
            $manager->persist($produit);
            $manager->flush();
        }
    }

    public function supprimerUtilisateur(int $id_utilisateur_a_supprimer){
        $utilisateur_a_supp = $this->utilisateurRepository->find($id_utilisateur_a_supprimer);

        $panier_client=$this->panierDeUtilisateur($utilisateur_a_supp);
        if($panier_client != null){
            foreach ($panier_client as $prod){
                $produit = $prod->getProduit();
                $produit->setQuantiteEnStock($produit->getQuantiteEnStock() + $prod->getQuantite());
                $this->em->persist($produit);
                $this->em->remove($prod);
            }
        }
        $this->em->remove($utilisateur_a_supp);
        $this->em->flush();


    }

}