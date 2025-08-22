<?php
namespace App\Form;

use App\Enum\InvoiceStatus;
use App\Model\InvoiceFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateFrom', DateType::class, [
                'label' => 'invoiceDate.from',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'html5' => true,
            ])
            ->add('dateTo', DateType::class, [
                'label' => 'invoiceDate.to',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'html5' => true,
            ])
            ->add('totalMin', NumberType::class, [
                'label' => 'total.from',
                'required' => false,
                'html5' => true,
                'scale' => 2,
                'attr' => ['step' => '0.01', 'placeholder' => '0,00'],
            ])
            ->add('totalMax', NumberType::class, [
                'label' => 'total.to',
                'required' => false,
                'html5' => true,
                'scale' => 2,
                'attr' => ['step' => '0.01', 'placeholder' => '0,00'],
            ])
            ->add('status', EnumType::class, [
                'class' => InvoiceStatus::class,
                'choice_label' => fn (InvoiceStatus $status) => $status->label(),
                'required' => false,
                'placeholder' => 'choose_empty',
                'empty_data' => null,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoiceFilter::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
