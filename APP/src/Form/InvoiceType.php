<?php

namespace App\Form;

use App\Entity\Invoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customer')
            ->add('company')
            ->add('invoiceNumber', null, [
                'disabled' => $builder->getData() && $builder->getData()->getId() !== null,
            ])
            ->add('invoiceDate')
            ->add('total', NumberType::class, [
                'scale' => 2,
                'html5' => true,
                'mapped' => false,
            ])
            ->add('invoiceItems', CollectionType::class, [
                'entry_type'   => InvoiceItemType::class,
                'entry_options'=> ['label' => false],
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype'    => true,
                'delete_empty' => true,
            ]);
            ;


        $builder->get('total')
            ->addModelTransformer(new CallbackTransformer(
                fn ($value) => $value !== null ? (float) $value : null,
                fn ($value) => $value !== null ? number_format((float) $value, 2, '.', '') : null
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
