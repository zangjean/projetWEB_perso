<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Service\UtilsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/utilisateur', name: 'utilisateur')]
final class UtilisateurController extends AbstractController
{
    #[Route('/inscription', name: '_inscription')]
    public function inscriptionAction(UserPasswordHasherInterface $hasher, UtilsService $utilsService ): Response
    {
        $em = $utilsService->get_entity_manager();
        $request = $utilsService->get_request();
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
}
