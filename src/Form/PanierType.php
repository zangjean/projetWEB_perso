<?php

namespace App\Form;

use App\Entity\Panier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class PanierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $max = $options['quantite_max'];
        $min = $options['quantite_min'];

        $builder
            ->add('quantite', ChoiceType::class, [
                'required' => true,
                'label' => 'Quantité',
                'choices' => array_combine(range($min, $max), range($min, $max)),
                'data' => 0,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Panier::class,
            'quantite_max' => 100,
            'quantite_min' => 0,
        ]);
        $resolver->setDefined('quantite_max');
        $resolver->setDefined('quantite_min');
    }
}
