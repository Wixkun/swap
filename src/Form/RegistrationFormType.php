<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'https://geo.api.gouv.fr/communes?fields=nom,code,population&limit=100');
        $data = $response->toArray();

        $cityChoices = [];
        foreach ($data as $city) {
            $cityChoices[$city['nom']] = $city['nom'];
        }

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'Le mot de passe ne peut pas être vide.',
                        ]),
                        new Assert\Length([
                            'min' => 8,
                            'minMessage' => 'Le mot de passe doit contenir au moins 8 caractères.',
                        ]),
                        new Assert\Regex([
                            'pattern' => '/^(?=.*[A-Za-z])(?=.*\d).{8,}$/',
                            'message' => 'Le mot de passe doit contenir au moins 8 caractères, incluant des lettres et des chiffres.',
                        ]),
                    ],
                    'attr' => [
                        'class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black'
                    ]
                ],
                'second_options' => [
                    'label' => 'Retapez le mot de passe',
                    'attr' => [
                        'class' => 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black'
                    ]
                ],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
            ])
            ->add('firstName', TextType::class, [
                'label' => 'First Name',
                'mapped' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name',
                'mapped' => false,
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'mapped' => false,
                'attr' => [
                    'class' => 'city-autocomplete-field',
                    'autocomplete' => 'off',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => "S'inscrire",
                'attr' => [
                    'class' => 'w-full bg-black text-white font-semibold py-2 px-4 rounded-lg hover:bg-gray-900 transition'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
