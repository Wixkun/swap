<?php
// src/Form/ProfileType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputClasses = 'w-64 border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring focus:ring-blue-200';

        // Champ email en lecture seule (issu de l'entité User)
        $builder->add('email', TextType::class, [
            'attr'     => ['class' => $inputClasses],
            'disabled' => true,
        ]);

        // Champ pour modifier le mot de passe (non mappé)
        $builder->add('plainPassword', RepeatedType::class, [
            'type'            => PasswordType::class,
            'mapped'          => false,
            'required'        => false,
            'invalid_message' => 'Les mots de passe doivent correspondre.',
            'first_options'   => [
                'label' => 'Nouveau mot de passe',
                'attr'  => ['class' => $inputClasses, 'placeholder' => 'Entrez votre nouveau mot de passe'],
            ],
            'second_options'  => [
                'label' => 'Retapez le mot de passe',
                'attr'  => ['class' => $inputClasses, 'placeholder' => 'Retapez votre mot de passe'],
            ],
        ]);

        // Champs Agent (non mappés)
        $builder->add('pseudoAgent', TextType::class, [
            'mapped'   => false,
            'required' => false,
            'label'    => 'Pseudo',
            'attr'     => ['class' => $inputClasses, 'placeholder' => 'Entrez votre pseudo'],
        ]);
        $builder->add('phoneAgent', TextType::class, [
            'mapped'   => false,
            'required' => false,
            'label'    => 'Numéro de téléphone',
            'attr'     => ['class' => $inputClasses, 'placeholder' => 'Entrez votre numéro de téléphone'],
        ]);

        // Champs Customer (non mappés)
        $builder->add('firstNameCustomer', TextType::class, [
            'mapped'   => false,
            'required' => false,
            'label'    => 'Prénom',
            'attr'     => ['class' => $inputClasses, 'placeholder' => 'Entrez votre prénom'],
        ]);
        $builder->add('lastNameCustomer', TextType::class, [
            'mapped'   => false,
            'required' => false,
            'label'    => 'Nom',
            'attr'     => ['class' => $inputClasses, 'placeholder' => 'Entrez votre nom'],
        ]);
        $builder->add('cityCustomer', TextType::class, [
            'mapped'   => false,
            'required' => false,
            'label'    => 'Ville',
            'attr'     => [
                'class'        => $inputClasses . ' cityCustomer-autocomplete-field',
                'autocomplete' => 'off',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
