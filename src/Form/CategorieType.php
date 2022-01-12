<?php

namespace App\Form;

use App\Entity\Categorie;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class , [
            'label' => 'Nom',
            'attr' => ['Ex.: Type de cheveux'
            ]
        ])

        ->add('image', FileType::class , [
            'required' => false,
            'label' => 'photo principale',
            'mapped' => false,
            'help' => 'png, jpg, jpeg ou jp2 - 3 Mo maximum',
            'constraints' => [
                new Image([
                    'maxSize' => '3000k',
                    'mimeTypes' => [
                        'image/png',
                        'image/jpg',
                        'image/jpeg',
                        'image/jp2',
                    ],
                    'mimeTypesMessage' => 'Merci de sélectionner une iamge au format PNG, JPG, JPEG ou JP2'
                     ])
                 ]
            ])

        ->add('valider', SubmitType::class)
        ;    
    }
    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}