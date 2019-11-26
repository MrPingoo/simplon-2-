<?php

namespace App\Form;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
                [
                    'label'=> 'Titre',
                    'constraints' => [
                        new NotBlank()
                    ],
                ]
            )
            ->add('price', TextType::class,
                [
                    'label'=> 'Prix',
                    'constraints' => [
                        new NotBlank()
                    ],
                ]
            )
            ->add('description', TextareaType::class,
                [
                    'label'=> 'Description',
                    'constraints' => [
                        new NotBlank()
                    ],
                ]
            )
            ->add('shippingCost', TextType::class,
                [
                    'label'=> 'Prix des frais de port',
                    'constraints' => [
                        new NotBlank()
                    ],
                ]
            )
            ->add('address', TextType::class,
                [
                    'label'=> 'Adresse',
                    'constraints' => [
                        new NotBlank()
                    ],
                ]
            )
            ->add('lat', HiddenType::class)
            ->add('lng', HiddenType::class)
            // ->add('createdAt')
            // ->add('state')
            // ->add('stripeTransac')
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => 'App:Category',
                'choice_label' => 'name',
                'query_builder' => function (CategoryRepository $dr) use ( $options )  {
                    return $dr->createQueryBuilder('a')
                        ->orderBy('a.name', 'ASC');
                },
            ])
            ->add('pictures', CollectionType::class, array(
                'entry_type' => PictureType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'label' => false
            ))
            // ->add('createdBy')
            // ->add('buyer')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
