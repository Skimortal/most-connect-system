<?php

namespace App\Form;

use App\Entity\TarifPosition;
use App\Enum\TarifItemCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TarifPositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EnumType::class, [
                'class' => TarifItemCategory::class,
                'choice_label' => fn (TarifItemCategory $category) => $category->label(),
            ])
            ->add('numberLabel')
            ->add('name')
            ->add('tarifValue', MoneyType::class, [
                'currency' => 'EUR',
                'divisor'  => 1,     // falls du ganze Euro speicherst; bei Cent: 100
                'input'    => 'float',
                'html5'    => false,
                'required' => false,
                'attr'     => ['inputmode' => 'decimal', 'placeholder' => '0,00 €'],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'    => TarifPosition::class,
        ]);
    }
}
