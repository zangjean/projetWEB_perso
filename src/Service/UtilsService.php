<?php

namespace App\Service;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\User;
use App\Entity\Utilisateur;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class UtilsService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->em = $manager;
    }

    public function get_entity_manager(): EntityManagerInterface
    {
        return $this->em;
    }

    public function retourPanierUtilisateurAction(Request $request,Utilisateur $utilisateur,Produit $produit): ?Panier
    {
        $panier = $this->em->getRepository(Panier::class)->findAll();
        $queryBuilder_panier = $this->em->getRepository(Panier::class)->createQueryBuilder('panier')
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

    public function panierDeUtilisateur(Utilisateur $utilisateur,Request $request):array
    {
        $panier = $this->em->getRepository(Panier::class)->findAll();
        $queryBuilder_panier = $this->em->getRepository(Panier::class)->createQueryBuilder('panier')
            ->where('panier.utilisateur = :utilisateur')
            ->setParameter('utilisateur', $utilisateur);
        $panier = $queryBuilder_panier->getQuery()->getResult();

        return $panier;
    }


    public function quantitePanierUtil(int $id_utilisateur,Request $request): int
    {
        $panier = $this->panierDeUtilisateur($this->em->getRepository(Utilisateur::class)->find($id_utilisateur),$request);
        $quantite = 0;
        foreach ($panier as $prod){
            $quantite += $prod->getQuantite();
        }
        return $quantite;
    }

}