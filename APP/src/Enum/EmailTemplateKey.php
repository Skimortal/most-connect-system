<?php
namespace App\Enum;

enum EmailTemplateKey: string
{
    case PASSWORD_RESET_REQUEST = 'password_reset_request';

    public function label(): string
    {
        return match($this) {
            self::PASSWORD_RESET_REQUEST     => 'Passwort vergessen',
        };
    }
}
