<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Service\UtilsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

#[Route('/utilisateur', name: 'utilisateur')]
final class UtilisateurController extends AbstractController
{
    #[Route('/inscription', name: '_inscription')]
    public function inscriptionAction(UserPasswordHasherInterface $hasher, UtilsService $utilsService,Request $request ): Response
    {
        $em = $utilsService->get_entity_manager();

        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->add('valider', SubmitType::class,['label' => 'Valider']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $hasher->hashPassword($utilisateur, $utilisateur->getPassword());
            $utilisateur->setPassword($password);
            $utilisateur->setRoles(['ROLE_CLIENT']);
            $em->persist($utilisateur);
            $em->flush();
            $this->addFlash('info','Compte creer avec succes');
            return $this->redirectToRoute('acceuil');
        }
        if ($form->isSubmitted()){
            $this->addFlash('info','formulaire de creation de compte incorrect');
        }
        $args = array(
            'form_inscription' => $form->createView()
        );
        return $this->render('Utilisateur/inscription.html.twig',$args);
    }

    #[Route('/modifier_mon_compte', name: '_modifier_mon_compte')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function modifierMonComteAction(UtilsService $utilsService, UserPasswordHasherInterface $hasher,Request $request): Response
    {
        $em = $utilsService->get_entity_manager();
        $utilisateur = $this->getUser();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->add('valider', SubmitType::class,['label' => 'Valider']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password_temp = $form->get('password')->getData();
            if ($password_temp != null){
                $password = $hasher->hashPassword($utilisateur, $utilisateur->getPassword());
                $utilisateur->setPassword($password);
                $em->persist($utilisateur);
                $em->flush();
                $this->addFlash('info','modification du compte effectueé avec succes');
                if ($this->isGranted('ROLE_SUPER_ADMIN')){
                    return $this->redirectToRoute('acceuil');
                }else{
                    return $this->redirectToRoute('produit_liste_produits');
                }
            }else{
                $this->addFlash('info','formulaire de modification de compte incorrect');
            }
        }
        if ($form->isSubmitted()){
            $this->addFlash('info','formulaire de modification de compte incorrect');
        }
        $args = array(
            'form_modifier_compte' => $form
        );
        return $this->render('Utilisateur/modifier_mon_compte.html.twig',$args);



    }


    #[Route('/supprimer_utilisateur/{id_utilisateur}',  name: '_supprimer_client',requirements: ['id_utilisateur' => '\d+'])]
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_SUPER_ADMIN")'))]

    public function supprimerClientAction(int $id_utilisateur,UtilsService $utilsService): Response
    {
        $em = $utilsService->get_entity_manager();
        $utilisateur = $utilsService->get_utilisateurRepository()->find($id_utilisateur);
        if($id_utilisateur == $this->getUser()->getId())
        {
            $this->addFlash('info','Vous ne pouvez pas supprimer votre compte');
        }elseif ($utilisateur->getRoles()=='ROLE_SUPER_ADMIN')
            $this->addFlash('info','Vous ne pouvez pas supprimer un super administrateur');
        else{
            if($utilisateur){
                if($utilisateur->getRoles()=='ROLE_ADMIN')
                {
                    if($this->isGranted('ROLE_SUPER_ADMIN'))
                    {
                        $utilsService->supprimerUtilisateur($id_utilisateur);
                        $this->addFlash('info','Admin supprimé avec succes');
                    }else
                    {
                        $this->addFlash('info','Vous ne pouvez pas supprimer un administrateur');
                    }
                }else{
                    $utilsService->supprimerUtilisateur($id_utilisateur);
                    $this->addFlash('info','Client supprimé avec succes');
                }
            }else{
                $this->addFlash('info','Utilisateur non trouve');
            }
        }
        return $this->redirectToRoute('admin_gerer_clients');
    }
}
