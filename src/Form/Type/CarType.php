<?php

namespace App\Form\Type;

use ArrayAccess;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType ;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CarType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $form = $builder
                ->add('plate', TextType::class)
                ->add('brand', TextType::class)
                ->add('model', TextType::class)
                ->add('km', NumberType::class)
                ->add('engine', TextType::class)
                ->add('color', TextType::class)
                ->add('save', SubmitType::class)
                ->getForm();

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
