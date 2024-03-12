<?php

namespace App\Validator;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PasswordValidator
{
    /**
     * Validates a new password.
     * @param string $newPassword - Password to validate
     * @return string - Validated password
     */
    public function validate(string $newPassword):string{
        // todo: add regex
        $ok = true;

        if (!$ok){
            throw new HttpException(
                Response::HTTP_BAD_REQUEST,
                'New password does not match policy.'
            );
        }

       return $newPassword;
    }
}
