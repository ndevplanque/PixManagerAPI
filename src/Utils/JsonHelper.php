<?php

declare(strict_types=1);

namespace App\Utils;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;

class JsonHelper
{
    public function send(string $json): JsonResponse
    {
        return new JsonResponse(
            data: $json,
            status: Response::HTTP_OK,
            json: true
        );
    }

    public function created(string $json, string $location = null): JsonResponse
    {
        return new JsonResponse(
            data: $json,
            status: Response::HTTP_CREATED,
            headers: $location ? ["Location" => $location] : [],
            json: true
        );
    }

    public function noContent(): JsonResponse
    {
        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
