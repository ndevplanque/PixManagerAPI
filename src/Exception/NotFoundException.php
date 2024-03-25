<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class NotFoundException extends HttpException
{
    public function __construct( string $entity = '', ?\Throwable $previous = null, array $headers = [], int $code = 0)
    {
        parent::__construct(404, "$entity Not found", $previous, $headers, $code);
    }
}