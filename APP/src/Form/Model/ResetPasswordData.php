<?php
namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordData
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 4096)]
    public ?string $plainPassword = null;

    #[Assert\NotBlank]
    #[Assert\EqualTo(propertyPath: "plainPassword", message: "Passwörter stimmen nicht überein.")]
    public ?string $plainPasswordRepeat = null;
}

