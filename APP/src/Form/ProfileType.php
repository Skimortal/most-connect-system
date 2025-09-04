<?php
// src/Form/ProfileType.php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ->add('firstName')
            ->add('lastName')
            ->add('department')
            ->add('position')
            ->add('email')
            ->add('phoneNumber')
            ->add('mobileNumber')
            ->add('faxNumber')
            ->add('avatarFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(maxSize: '2M', mimeTypes: ['image/*'])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
    }
}
