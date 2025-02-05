<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputClasses = 'w-64 border border-gray-300 rounded-lg px-4 py-2 focus:border-blue-500 focus:ring focus:ring-blue-200';

        $builder
            ->add('email', TextType::class, [
                'attr' => ['class' => $inputClasses, 'placeholder' => 'Enter your email'],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped'   => false,   
                'required' => false,
                'label'    => 'Nouveau mot de passe',
                'attr'     => [
                    'class'       => $inputClasses,
                    'placeholder' => 'Change password',
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'Admin'    => 'ROLE_ADMIN',
                    'Agent'    => 'ROLE_AGENT',
                    'Customer' => 'ROLE_CUSTOMER',
                ],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('pseudoAgent', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Pseudo',
                'attr'     => ['class' => $inputClasses, 'placeholder' => 'Enter a pseudo'],
            ])
            ->add('phoneAgent', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Phone Number',
                'attr'     => ['class' => $inputClasses, 'placeholder' => 'Enter a phone number'],
            ])
            ->add('firstNameCustomer', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'First Name',
                'attr'     => ['class' => $inputClasses, 'placeholder' => 'Enter your first name'],
            ])
            ->add('lastNameCustomer', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'Last Name',
                'attr'     => ['class' => $inputClasses, 'placeholder' => 'Enter your last name'],
            ])
            ->add('cityCustomer', TextType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => 'City',
                'attr'     => [
                    'class'         => "$inputClasses cityCustomer-autocomplete-field", 
                    'autocomplete'  => 'off',
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
