<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Label;
use App\Validator\PayloadValidator;
use Symfony\Component\HttpFoundation\Request;

class LabelFactory
{
    public function __construct(
        private readonly PayloadValidator $payloadValidator,
    )
    {
    }

    public function fromRequest(Request $request): Label
    {
        $payload = $request->toArray();

        $this->payloadValidator->hasKeys($payload, [
            'name',
        ]);

        return new Label($payload['name']);
    }
}
