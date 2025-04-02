<?php

namespace App\Form;

use App\Entity\Pays;
use App\Entity\Utilisateur;
use Doctrine\DBAL\Types\StringType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login', null, [
                'label' => 'Login',
                'required' => true,
            ])
            //->add('roles')
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true,
                'attr' => ['placeholder' => ''],
            ])
            ->add('nom')
            ->add('prenom')
            ->add('dateDeNaissance',

                null, ['widget' => 'single_text',]
            )
            ->add('paysDAppartenance', EntityType::class, [
                'class' => Pays::class,
                'choice_label' => 'nom',
                'label' => 'Pays d\'appartenance',
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'query_builder' => function (\App\Repository\PaysRepository $repositoryPays) {
                    return $repositoryPays->createQueryBuilder('pays')
                        ->orderBy('pays.nom', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
