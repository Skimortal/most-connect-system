<?php
// src/Form/ProfileType.php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password',PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'attr'     => ['placeholder' => '********'],
                'help'     => 'Leer lassen, um das aktuelle Passwort zu behalten.',
                'always_empty' => true,
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
        $resolver->setDefaults(['data_class' => User::class]);
    }
}
