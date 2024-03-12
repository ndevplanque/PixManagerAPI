<?php

namespace App\Factory;

use App\Entity\Label;
use App\Validator\PayloadValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LabelFactory
{
    private readonly PayloadValidator $payloadValidator;

    public function __construct(PayloadValidator $payloadValidator)
    {
        $this->payloadValidator = $payloadValidator;
    }

    public function fromRequest(Request $request): Label
    {
        $payload = $request->toArray();
        $this->validate($payload);
        return new Label($payload['name']);
    }

    /**
     * @throws HttpException
     */
    private function validate(array $payload): void
    {
        $this->payloadValidator->hasKeys($payload, [
            'name',
        ]);
    }
}
