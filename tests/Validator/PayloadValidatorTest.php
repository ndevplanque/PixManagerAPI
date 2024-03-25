<?php

namespace Tests\App\Validator;

use App\Validator\PayloadValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PayloadValidatorTest extends TestCase
{
    private readonly PayloadValidator $validator;

    public function setUp(): void
    {
        $this->validator = new PayloadValidator();
    }

    public function testHasKeysShouldThrowOnMissingKey(): void
    {
        $payload = [
            'useful_key1' => 'data1',
            'useful_key2' => 'data2',
            'useless_key1' => 'data4',
        ];

        $keys = [
            'useful_key1',
            'useful_key2',
            'useful_key3',
        ];

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("Property 'useful_key3' is missing");

        $this->validator->hasKeys($payload, $keys);
    }

    public function testHasKeys(): void
    {
        $payload = [
            'useful_key1' => 'data1',
            'useful_key2' => 'data2',
            'useful_key3' => 'data3',
            'useless_key1' => 'data4',
        ];

        $keys = [
            'useful_key1',
            'useful_key2',
            'useful_key3',
        ];

        $this->expectNotToPerformAssertions();

        $this->validator->hasKeys($payload, $keys);
    }
}
