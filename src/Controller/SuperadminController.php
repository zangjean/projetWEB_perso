<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Service\UtilsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/superadmin', name: 'superadmin')]
final class SuperadminController extends AbstractController
{

    #[Route('/gerer_admin', name: '_gerer_admin')]
    public function gererAdminAction(UtilsService $utilsService): Response
    {
        $em = $utilsService->get_entity_manager();
        $utilisateurRepository = $em->getRepository(Utilisateur::class);
        $queryBuilder = $utilisateurRepository->createQueryBuilder('admin')
            ->where('admin.roles LIKE :roles')
            ->setParameter('roles', '%ROLE_ADMIN%');
        $admins = $queryBuilder->getQuery()->getResult();

        $args = ['admins' => $admins];

        return $this->render('Superadmin/gerer_admin.html.twig', $args);
    }

    #[Route('/supprimer_admin/{id_admin}',
        name: '_supprimer_admin'
    )]
    public function supprimerAdminAction($id_admin,UtilsService $utilsService): Response
    {
        $em = $utilsService->get_entity_manager();

        $utilisateur = $em->getRepository(Utilisateur::class)->find($id_admin);
        if($utilisateur)
        {
            $em->remove($utilisateur);
            $em->flush();
            $this->addFlash('info','Admin supprimer avec succes');
        }else{
            $this->addFlash('info','Admin non trouve');
        }
        return $this->redirectToRoute('superadmin_gerer_admin');

    }

    #[Route('/ajouter_admin', name: '_ajouter_admin')]
    public function ajouterAdminAction(UtilsService $utilsService,UserPasswordHasherInterface $hasher,Request $request): Response
    {
        $em = $utilsService->get_entity_manager();

        $newAdmin = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $newAdmin);
        $form->add('valider', SubmitType::class,['label' => 'Valider']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $loginTemp = $form->get('login')->getData();

            $qb=$em->getRepository(Utilisateur::class)->createQueryBuilder('admin')
                ->where('admin.login = :login')
                ->setParameter('login',$loginTemp);
            $result = $qb->getQuery()->getResult();
            if($result){
                $this->addFlash('info','login déja utilisé');
            }else{
                $password = $hasher->hashPassword($newAdmin, $newAdmin->getPassword());
                $newAdmin->setPassword($password);
                $newAdmin->setRoles(['ROLE_ADMIN']);
                $em->persist($newAdmin);
                $em->flush();
                $this->addFlash('info','Admin creer avec succes');
                return $this->redirectToRoute('superadmin_gerer_admin');
            }
        }else{
            $this->addFlash('info','formulaire de creation de compte incorrect');
        }
        $args = array(
            'form_inscription' => $form
        );
        return $this->render('Superadmin/ajouter_admin.html.twig', $args);
    }
}
