<?php

namespace Tests\App\Validator;

use App\Validator\PasswordValidator;
use PHPUnit\Framework\TestCase;

class PasswordValidatorTest extends TestCase
{
    private readonly PasswordValidator $validator;

    public function setUp(): void
    {
        $this->validator = new PasswordValidator();
    }

    public function testValidate(): void
    {
        $this->markTestIncomplete('TODO: No password validation policy configured yet.');
    }
}
