<?php
namespace App\Form;

use App\Form\Model\ResetPasswordData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Neues Passwort',
                'attr' => ['autocomplete' => 'new-password'],
            ])
            ->add('plainPasswordRepeat', PasswordType::class, [
                'label' => 'Neues Passwort (Wiederholung)',
                'attr' => ['autocomplete' => 'new-password'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ResetPasswordData::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'reset_password',
        ]);
    }
}
