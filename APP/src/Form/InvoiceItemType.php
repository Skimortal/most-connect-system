<?php
namespace App\Form;

use App\Entity\InvoiceItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description', TextareaType::class, [])
            ->add('quantity')
            ->add('unitPrice', MoneyType::class, [
                'currency' => 'EUR',
                'divisor'  => 1,     // falls du ganze Euro speicherst; bei Cent: 100
                'input'    => 'float',
                'html5'    => false,
                'required' => false,
                'attr'     => ['inputmode' => 'decimal', 'placeholder' => '0,00 €'],
            ])
            ->add('taxRate')
            ->add('total', NumberType::class, [
                'scale' => 2,
                'html5' => true,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => InvoiceItem::class]);
    }
}
