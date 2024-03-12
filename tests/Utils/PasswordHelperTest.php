<?php

namespace Tests\App\Utils;

use App\Utils\PasswordHelper;
use App\Validator\PasswordValidator;
use PHPUnit\Framework\TestCase;

class PasswordHelperTest extends TestCase
{
    private readonly PasswordHelper $passwordHelper;
    private readonly PasswordValidator $passwordValidator;

    public function setUp(): void
    {
        $this->passwordHelper = new PasswordHelper(
            $this->passwordValidator = $this->createMock(PasswordValidator::class),
        );
    }

    public function testHash(): void
    {
        $this->passwordValidator
            ->expects($this->once())
            ->method('validate')
            ->with($newPassword = 'azerty')
            ->willReturn($newPassword);

        $hashResult = $this->passwordHelper->hash($newPassword);

        // hashResult should not be equal to given password
        $this->assertNotEquals($newPassword, $hashResult);

        // but password_verify should work
        $this->assertTrue(password_verify($newPassword, $hashResult),);
    }
}
