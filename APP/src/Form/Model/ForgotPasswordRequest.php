<?php
namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ForgotPasswordRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;
}
