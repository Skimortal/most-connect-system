<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('roles', ChoiceType::class, [
                'label' => 'Rollen',
                'choices' => array_combine(
                    array_map(fn(UserRole $r) => $r->label(), UserRole::cases()), // Labels
                    array_map(fn(UserRole $r) => $r->value, UserRole::cases())     // Werte (Strings)
                ),
                'multiple' => true,
                'expanded' => true,                 // Checkboxen
                'choice_translation_domain' => false,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => !$options['is_edit'], // beim Anlegen Pflicht, beim Edit optional
                'invalid_message' => 'Die Passwörter stimmen nicht überein.',
                'first_options'  => ['label' => 'password.label'],
                'second_options' => ['label' => 'passwordRepeat.label'],
                'constraints' => $options['is_edit'] ? [] : [
                    new NotBlank(['message' => 'Bitte ein Passwort eingeben']),
                    new Length(['min' => 8, 'minMessage' => 'Mind. {{ limit }} Zeichen']),
                    // new NotCompromisedPassword(), // optional
                ],
            ])
            ->add('company', EntityType::class, [
                'class' => Company::class,
                'required' => false,
                'placeholder' => '--',
            ])
            ->add('firstName')
            ->add('lastName')
            ->add('department')
            ->add('position')
            ->add('email')
            ->add('phoneNumber')
            ->add('mobileNumber')
            ->add('faxNumber')
            ->add('isActive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
    }
}
