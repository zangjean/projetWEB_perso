<?php

namespace App\Form;

use App\Entity\Pays;
use App\Entity\Produit;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prixUnitaire',null, ['attr' => ['min' => 0.01,'step' => 0.01]])
            ->add('quantiteEnStock',null, ['attr' => ['min' => 1]])
            ->add('description')
            ->add('payss', EntityType::class, [
                'class' => Pays::class,
                'choice_label' => 'nom',
                'label' => 'Pays',
                'expanded' => true,
                'required' => true,
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
