<?php

namespace App\Form;

use App\Entity\Company;
use App\Enum\CompanyCategoryType;
use App\Enum\CompanyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyTypeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EnumType::class, [
                'class' => CompanyType::class,
            ])
            ->add('categories', ChoiceType::class, [
                'choices' => $this->getCategoryChoices(),
                'choice_label' => fn(CompanyCategoryType $sector) => $sector->label(),
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('uid')
            ->add('name')
            ->add('phone')
            ->add('email')
            ->add('website')
            ->add('logoFile', FileType::class, [
                'required' => false,
            ])
            ->add('logoSmallFile', FileType::class, [
                'required' => false,
            ])
            ->add('bancAccountInstitute')
            ->add('bancAccountOwner')
            ->add('bancAccountIBAN')
            ->add('bancAccountBIC')
        ;
    }

    private function getCategoryChoices(): array
    {
        $choices = [];
        foreach (CompanyCategoryType::cases() as $category) {
            $choices[$category->value] = $category;
        }
        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
