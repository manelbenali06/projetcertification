<?php

namespace App\Form;

use App\Entity\Produit;

use App\Entity\Categorie;
use App\Entity\Ingredient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class Produit1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
    
    
            ->add('nom', TextType::class , [
            'label' => 'Nom',
            'attr' => ['Ex.: Shampoing'
    
            ]
        ])
            ->add('image', FileType::class , [
            'required' => false,
            'label' => 'shampoing',
            'mapped' => false,
            'help' => 'png, jpg, jpeg ou jp2 - 1 Mo maximum',
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
    
            ->add('description', TextareaType::class , [
            'attr' => [
            'placeholder' => 'Ex.:Shampoing HairCare'
            ]
        ])
        
        ->add('volume', IntegerType::class , [
            'label' => 'volume (ml)',
            'attr' => [
                'placeholder' => 'Ex.: 250 ml',
                'min' => 0
            ]
        ])
            ->add('prix', IntegerType::class , [
            'label' => 'prix (€)',
            'attr' => [
            'placeholder' => 'Ex.: 123 456',
            'min' => 0
            ]
        ])
    
            ->add('categorie', EntityType::class , [
            'class' => Categorie::class ,
            'choice_label' => 'nom'
        ])
    
    
    
            ->add('ingredient', EntityType::class , [
            'class' => Ingredient::class ,
            'choice_label' => 'nom',
            'multiple' => true,
            'expanded' => true
        ])
    
    
            ->add('valider', SubmitType::class)
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => Produit::class ,
            ]);
        }
    }