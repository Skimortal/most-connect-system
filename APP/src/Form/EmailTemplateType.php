<?php
// src/Form/EmailTemplateType.php
namespace App\Form;

use App\Entity\EmailTemplate;
use App\Enum\EmailTemplateKey;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Attribute\AsFormType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[AsFormType] // Symfony 6.3+ / 7: registriert den Type als Service
final class EmailTemplateType extends AbstractType
{
    public function __construct(private Security $security) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('templateKey', EnumType::class, [
                'class' => EmailTemplateKey::class,
                'choice_label' => fn (EmailTemplateKey $status) => $status->label(),
            ])

            // Locale-Auswahl (oder Textfeld, falls du freie Locales willst)
            ->add('locale', LocaleType::class, [
                'label' => 'Locale',
                'preferred_choices' => ['de', 'de_AT', 'en', 'sr'],
            ])

            ->add('subjectTemplate', TextareaType::class, [
                'attr' => ['rows' => 2, 'spellcheck' => 'false', 'data-code' => 'twig'],
            ])

            ->add('htmlTemplate', TextareaType::class, [
                'attr' => ['rows' => 12, 'spellcheck' => 'false', 'data-code' => 'twig'],
            ])

            ->add('textTemplate', TextareaType::class, [
                'required' => false,
                'attr' => ['rows' => 8, 'spellcheck' => 'false', 'data-code' => 'twig'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EmailTemplate::class,
            'translation_domain' => 'admin', // optional
        ]);
    }
}
