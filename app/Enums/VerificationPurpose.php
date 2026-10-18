<?php

namespace App\Enums;

enum VerificationPurpose: string
{
    case EmailVerification = 'email_verification';
    case PasswordReset = 'password_reset';
    case Login = 'login';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
