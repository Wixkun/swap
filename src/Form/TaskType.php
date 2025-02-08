<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'En attente' => 'pending',
                    'Annulé'     => 'cancel',
                    'Terminé'    => 'done',
                ],
            ])
            ->add('imageFiles', FileType::class, [
                'label' => 'Ajouter des images',
                'mapped' => false,
                'multiple' => true,
                'required' => false,
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '5M',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/png',
                                    'image/gif',
                                    'image/webp',
                                ],
                                'mimeTypesMessage' => 'Veuillez uploader uniquement des images (JPEG, PNG, GIF, WEBP).',
                            ])
                        ],
                    ]),
                ],
            ])
            ->add('updatedAt', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ]);
        if (!empty($options['existing_images'])) {
            $choices = array_combine($options['existing_images'], $options['existing_images']);
            $builder->add('removeImages', ChoiceType::class, [
                'mapped' => false,
                'choices' => $choices,
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'label' => 'Supprimer les images existantes',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'existing_images' => [],
        ]);
    }
}
