<?php

namespace Tests\App\Factory;

use App\Factory\LabelFactory;
use App\Validator\PayloadValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class LabelFactoryTest extends TestCase
{
    private readonly LabelFactory $factory;
    private readonly PayloadValidator $payloadValidator;

    public function setUp(): void
    {
        $this->factory = new LabelFactory(
            $this->payloadValidator = $this->createMock(PayloadValidator::class),
        );
    }

    public function testFromRequest(): void
    {
        $request = $this->createConfiguredMock(Request::class, [
            'toArray' => $payload = ['name' => 'cats'],
        ]);

        $this->payloadValidator
            ->expects($this->once())
            ->method('hasKeys')
            ->with($payload, ['name']);

        $this->factory->fromRequest($request);
    }
}
