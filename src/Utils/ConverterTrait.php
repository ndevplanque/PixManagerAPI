<?php

declare(strict_types=1);

namespace App\Utils;

trait ConverterTrait
{
    public function asBool(mixed $value): ?bool
    {
        if ($value === null) {
            return null;
        }

        return (bool)$value;
    }

    public function asString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string)$value;
    }
}