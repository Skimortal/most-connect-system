<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Enum\InvoiceDesign;
use App\Enum\InvoiceStatus;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $customers = $options['customers'] ?? [];
        $hide_customer = $options['hide_customer'] ?? false;
        $hide_company = $options['hide_company'] ?? false;

        if(!$hide_customer) {
            $builder->add('customer', EntityType::class, [
                'class'        => Customer::class,
                'choices'      => $customers,      // nur erlaubte Kunden
                'placeholder'  => 'Bitte Kunde wählen',
                'required'     => true,
            ]);
        }

        if(!$hide_company) {
            $builder->add('company', EntityType::class, [
                'class'        => Company::class,
                'placeholder'  => 'Bitte Firma wählen',
                'required'     => true,
            ]);
        }

        $builder
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
            ])
            ->add('status', EnumType::class, [
                'class' => InvoiceStatus::class,
                'choice_label' => fn (InvoiceStatus $status) => $status->label(),
            ])
            ->add('entryText', TextareaType::class, [
                'required' => false,
                'empty_data' => null,
            ])
            ->add('paymentInfoText', TextareaType::class, [
                'required' => false,
                'empty_data' => null,
            ])
            ->add('bottomText', TextareaType::class, [
                'required' => false,
                'empty_data' => null,
            ])
            ->add('design', ChoiceType::class, [
                'label' => 'Design',
                'choices' => [
                    'Minimal'    => InvoiceDesign::MINIMAL,
                    'Modern'     => InvoiceDesign::MODERN,
                    'Letterhead' => InvoiceDesign::LETTERHEAD,
                    'Grid'       => InvoiceDesign::GRID,
                    'Elegant'    => InvoiceDesign::ELEGANT,
                ],
                'expanded' => true,   // Radios nebeneinander
                'multiple' => false,
                // wir liefern pro Choice die Preview-URL als data-Attribut
                'choice_attr' => function (?InvoiceDesign $choice) {
                    if (!$choice) return [];
                    return [
                        'data-preview' => '/images/invoice_designs/'.$choice->value.'.jpg',
                        'class' => 'design-radio',
                    ];
                },
            ])
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
            'data_class'    => Invoice::class,
            'customers'     => [],
            'hide_customer' => false,
            'hide_company' => true,
        ]);

        $resolver->setAllowedTypes('customers', ['array', Collection::class]);
        $resolver->setAllowedTypes('hide_customer', 'bool');
    }
}
