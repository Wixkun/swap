<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Skill;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AgentRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'mapped' => false,
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'Numéro de téléphone',
                'mapped' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => true,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'constraints' => [
                        new Assert\NotBlank(['message' => 'Le mot de passe ne peut pas être vide.']),
                        new Assert\Length([
                            'min' => 8,
                            'minMessage' => 'Le mot de passe doit contenir au moins 8 caractères.',
                        ]),
                        new Assert\Regex([
                            'pattern' => '/^(?=.*[A-Za-z])(?=.*\d).{8,}$/',
                            'message' => 'Le mot de passe doit contenir au moins 8 caractères, incluant des lettres et des chiffres.',
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Retapez le mot de passe',
                ],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
            ])
            ->add('skills', EntityType::class, [
                'class' => Skill::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'mapped' => false,
                'label' => 'Vos Skills',
            ])
            ->add('submit', SubmitType::class, [
                'label' => "S'inscrire",
                'attr' => ['class' => 'w-full bg-black text-white font-semibold py-2 px-4 rounded-lg hover:bg-gray-900 transition']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
