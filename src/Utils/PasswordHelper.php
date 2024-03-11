<?php

namespace App\Utils;

use App\Validator\PasswordValidator;

class PasswordHelper
{
    private readonly PasswordValidator $passwordValidator;

    public function __construct()
    {
        $this->passwordValidator = new PasswordValidator();
    }

    public function hash(string $newPassword): string
    {
        return password_hash(
            $this->passwordValidator->validate($newPassword),
            PASSWORD_BCRYPT
        );
    }
}
