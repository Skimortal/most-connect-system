<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('company')
            ->add('type', EnumType::class, [
                'class' => \App\Enum\CustomerType::class,
                'choice_label' => fn (\App\Enum\CustomerType $status) => $status->label(),
            ])
            ->add('companyName')
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('street')
            ->add('zip')
            ->add('city')
            ->add('country')
            ->add('uid')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
