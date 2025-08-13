<?php

namespace App\Form;

use App\Entity\TaxRate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxRateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('company')
            ->add('name')
            ->add('rate', NumberType::class, [
                'scale' => 2,
                'html5' => true,
            ])
        ;


        $builder->get('rate')
            ->addModelTransformer(new CallbackTransformer(
            // model → view
                fn ($value) => $value !== null ? (float) $value : null,
                // view → model
                fn ($value) => $value !== null ? number_format((float) $value, 2, '.', '') : null
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TaxRate::class,
        ]);
    }
}
