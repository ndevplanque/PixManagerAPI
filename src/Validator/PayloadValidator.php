<?php

namespace App\Validator;

use Symfony\Component\HttpKernel\Exception\HttpException;

class PayloadValidator
{
    /**
     * @throws HttpException
     */
    public function hasKeys(array $payload, array $keys): void
    {
        for ($i = 0; $i < count($keys); $i++) {
            if (!array_key_exists($keys[$i], $payload)) {
                throw new HttpException(400, "Property '$keys[$i]' is missing");
            }
        }
    }
}
