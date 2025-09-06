<?php

namespace App\Form;

use App\Entity\Tarif;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TarifType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('validFrom')
            ->add('validTo')
            ->add('description', TextareaType::class, [
                'required' => false,
                'empty_data' => null,
            ])
            ->add('furtherInfo', TextareaType::class, [
                'required' => false,
                'empty_data' => null,
            ])
            ->add('active')
            ->add('positions', CollectionType::class, [
                'entry_type'   => TarifPositionType::class,
                'entry_options'=> ['label' => false],
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype'    => true,
                'delete_empty' => true,
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'    => Tarif::class,
        ]);
    }
}
