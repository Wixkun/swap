<?php
// src/Form/BecomeCustomerType.php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BecomeCustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr'  => ['class' => 'w-full px-4 py-2 border border-gray-300 rounded-md'],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr'  => ['class' => 'w-full px-4 py-2 border border-gray-300 rounded-md'],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'mapped' => false,
                'attr' => [
                    'class' => 'city-autocomplete-field',
                    'autocomplete' => 'off',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
