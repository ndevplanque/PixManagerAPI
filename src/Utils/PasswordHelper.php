<?php

declare(strict_types=1);

namespace App\Utils;

use App\Validator\PasswordValidator;

class PasswordHelper
{
    public function __construct(
        private readonly PasswordValidator $passwordValidator
    ) {}

    public function hash(string $newPassword): string
    {
        return password_hash(
            password: $this->passwordValidator->validate($newPassword),
            algo: PASSWORD_BCRYPT
        );
    }
}
