<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class)
            ->add('password', PasswordType::class)
            ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'Admin' => 'ROLE_ADMIN',
                    'Agent' => 'ROLE_AGENT',
                    'Customer' => 'ROLE_CUSTOMER',
                ],
                'expanded' => true, 
                'multiple' => true,  
            ])
            ->add('pseudoAgent', TextType::class, [ 
                'mapped' => false, 
                'required' => false,
                'label' => 'Pseudo',
                'attr' => ['placeholder' => 'Enter a pseudo']
            ])
            ->add('phoneAgent', TextType::class, [
                'mapped' => false, 
                'required' => false,
                'label' => 'Phone Number',
                'attr' => [
                    'placeholder' => 'Enter a phone number',
                    'maxlength' => 12, 
                    'pattern' => '.{10,12}', 
                    'title' => 'Phone number must be between 10 and 12 characters.'
                ],
                'constraints' => [
                    new Assert\Length([
                        'max' => 12,
                        'maxMessage' => 'The phone number cannot be longer than 12 characters.'
                    ])
                ]
            ])
            ->add('firstNameCustomer', TextType::class, [
                'mapped' => false, 
                'required' => false,
                'label' => 'First Name',
                'attr' => ['placeholder' => 'Enter your first name']
            ])
            ->add('lastNameCustomer', TextType::class, [
                'mapped' => false, 
                'required' => false,
                'label' => 'Last Name',
                'attr' => ['placeholder' => 'Enter your last name']
            ])
            ->add('cityCustomer', TextType::class, [
                'mapped' => false, 
                'required' => false,
                'label' => 'City',
                'attr' => [
                    'placeholder' => 'Enter your city',
                    'class' => 'cityCustomer-autocomplete-field', 
                    'autocomplete' => 'off'
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
