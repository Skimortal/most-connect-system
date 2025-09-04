<?php
namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultFormOptionsExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        // Wir erweitern den Basis-FormType, so dass alle Formulare betroffen sind
        return [FormType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Globale Übersetzungs-Domain für alle Form-Labels
            'translation_domain' => 'messages',
            // Generisches Label-Format: entität.feld.label
            'label_format'       => '%name%.label',
        ]);
    }
}
