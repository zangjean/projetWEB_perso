<?php

namespace App\DataFixtures;

use App\Entity\Pays;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateurFixtures extends Fixture
{

    private ?UserPasswordHasherInterface $passwordHasher = null;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }


    public function load(ObjectManager $manager,): void
    {
        // $product = new Product();
        // $manager->persist($product);

        /*
        $user2 = new User();
        $user2
            ->setLogin('gilles')
            ->setRoles(['ROLE_ADMIN'])
            ->setName('gilles')
            ->setFirstname('Subrenat');
        $hashedPassword = $this->passwordHasher->hashPassword($user2, 'sellig');
        $user2->setPassword($hashedPassword);
        $manager->persist($user2);

        $user3 = new User();
        $user3
            ->setLogin('rita')
            ->setRoles(['ROLE_CLIENT'])
            ->setName('rita')
            ->setFirstname('Zrour');
        $hashedPassword = $this->passwordHasher->hashPassword($user3, 'atir');
        $user3->setPassword($hashedPassword);
        $manager->persist($user3);

        $user4 = new User();
        $user4
            ->setLogin('boumediene')
            ->setRoles(['ROLE_CLIENT'])
            ->setName('saidi')
            ->setFirstname('boumediene');
        $hashedPassword = $this->passwordHasher->hashPassword($user4, 'eneidemuob');
        $user4->setPassword($hashedPassword);
        $manager->persist($user4);
        */


        $utilisateur1 = new Utilisateur();
        $utilisateur1
            ->setNom('admin')
            ->setLogin('admin')
            ->setPrenom('admin')
            ->setRoles(['ROLE_ADMIN'])
            ->setDateDeNaissance(new \DateTime('2000-01-01'))
            ->setPaysDAppartenance($this->getReference('FR', Pays::class));
        $hashedPassword = $this->passwordHasher->hashPassword($utilisateur1, 'admin');
        $utilisateur1->setPassword($hashedPassword);
        $manager->persist($utilisateur1);

        $utilisateur2 = new Utilisateur();
        $utilisateur2
            ->setNom('Subrenat')
            ->setLogin('gilles')
            ->setPrenom('gilles')
            ->setRoles(['ROLE_ADMIN'])
            ->setDateDeNaissance(new \DateTime('2000-01-01'))
            ->setPaysDAppartenance($this->getReference('FR', Pays::class));
        $hashedPassword = $this->passwordHasher->hashPassword($utilisateur2, 'sellig');
        $utilisateur2->setPassword($hashedPassword);
        $manager->persist($utilisateur2);

        $utilisateur3 = new Utilisateur();
        $utilisateur3
            ->setNom('Zrour')
            ->setLogin('rita')
            ->setPrenom('rita')
            ->setRoles(['ROLE_CLIENT'])
            ->setDateDeNaissance(new \DateTime('2000-01-01'))
            ->setPaysDAppartenance($this->getReference('FR', Pays::class));
        $hashedPassword = $this->passwordHasher->hashPassword($utilisateur3, 'atir');
        $utilisateur3->setPassword($hashedPassword);
        $manager->persist($utilisateur3);

        $utilisateur4 = new Utilisateur();
        $utilisateur4
            ->setNom('boumediene')
            ->setLogin('boumediene')
            ->setPrenom('saidi')
            ->setRoles(['ROLE_CLIENT'])
            ->setDateDeNaissance(new \DateTime('2000-01-01'))
            ->setPaysDAppartenance($this->getReference('FR', Pays::class));
        $hashedPassword = $this->passwordHasher->hashPassword($utilisateur4, 'eneidemuob');
        $utilisateur4->setPassword($hashedPassword);
        $manager->persist($utilisateur4);

        $utilisateur5 = new Utilisateur();
        $utilisateur5
            ->setNom('sadmin')
            ->setLogin('sadmin')
            ->setPrenom('sadmin')
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setDateDeNaissance(new \DateTime('2000-01-01'))
            ->setPaysDAppartenance($this->getReference('FR', Pays::class));
        $hashedPassword = $this->passwordHasher->hashPassword($utilisateur5, 'superadmin');
        $utilisateur5->setPassword($hashedPassword);
        $manager->persist($utilisateur5);

        $utilisateur6 = new Utilisateur();
        $utilisateur6
            ->setNom('Zang')
            ->setLogin('jeanzang')
            ->setPrenom('jean')
            ->setRoles(['ROLE_CLIENT', 'ROLE_ADMIN'])
            ->setDateDeNaissance(new \DateTime('2001-07-21'))
            ->setPaysDAppartenance($this->getReference('FR', Pays::class));
        $hashedPassword = $this->passwordHasher->hashPassword($utilisateur6, 'zang');
        $utilisateur6->setPassword($hashedPassword);
        $manager->persist($utilisateur6);



        $manager->flush();
    }
}
