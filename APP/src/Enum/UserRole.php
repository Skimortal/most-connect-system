<?php
namespace App\Enum;

enum UserRole: string
{
    case USER   = 'ROLE_USER';
    case EDITOR = 'ROLE_EDITOR';
    case ADMIN  = 'ROLE_ADMIN';
    case SUPERUSER = 'ROLE_SUPERUSER';

    public function label(): string
    {
        return match ($this) {
            self::USER   => 'Benutzer',
            self::EDITOR => 'Editor',
            self::ADMIN  => 'Admin',
            self::SUPERUSER => 'Superadmin',
        };
    }
}
