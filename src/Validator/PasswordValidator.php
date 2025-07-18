<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PasswordValidator
{
    // === Password Policy Constants ===
    private const MIN_LENGTH = 8;
    private const REGEX_UPPERCASE = '/[A-Z]/';
    private const REGEX_LOWERCASE = '/[a-z]/';
    private const REGEX_DIGIT = '/\d/';
    private const REGEX_SPECIAL_CHAR = '/[\W_]/';
    private const REGEX_WHITESPACE = '/\s/';

    /**
     * Validates a new password according to the following policy:
     * - Minimum length: 8 characters
     * - At least one uppercase letter (A–Z)
     * - At least one lowercase letter (a–z)
     * - At least one numeric digit (0–9)
     * - At least one special character (e.g. !@#$%^&*)
     * - No whitespace characters allowed (spaces, tabs, etc.)
     *
     * @param string $newPassword The password to validate
     * @return string The validated password if it meets the policy
     * @throws HttpException If the password fails to meet the policy requirements
     */
    public function validate(string $newPassword): string
    {
        $policyErrors = [];

        if (strlen($newPassword) < self::MIN_LENGTH) {
            $policyErrors[] = sprintf('Password must be at least %d characters long.', self::MIN_LENGTH);
        }

        if (!preg_match(self::REGEX_UPPERCASE, $newPassword)) {
            $policyErrors[] = 'Password must contain at least one uppercase letter.';
        }

        if (!preg_match(self::REGEX_LOWERCASE, $newPassword)) {
            $policyErrors[] = 'Password must contain at least one lowercase letter.';
        }

        if (!preg_match(self::REGEX_DIGIT, $newPassword)) {
            $policyErrors[] = 'Password must contain at least one number.';
        }

        if (!preg_match(self::REGEX_SPECIAL_CHAR, $newPassword)) {
            $policyErrors[] = 'Password must contain at least one special character.';
        }

        if (preg_match(self::REGEX_WHITESPACE, $newPassword)) {
            $policyErrors[] = 'Password must not contain any whitespace characters.';
        }

        if (!empty($policyErrors)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, implode(' ', $policyErrors));
        }

        return $newPassword;
    }
}
