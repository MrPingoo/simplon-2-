<?php

namespace App\Form;

use App\Entity\Pizza;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PizzaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('price')
            ->add('category', EntityType::class,
                array(
                    'class' => 'App:Category',
                    'choice_label' => 'name',
                    'query_builder' => function (CategoryRepository $cr) use ( $options )  {
                        return $cr->createQueryBuilder('a')
                            ->orderBy('a.name', 'ASC');
                    }
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pizza::class,
        ]);
    }
}
