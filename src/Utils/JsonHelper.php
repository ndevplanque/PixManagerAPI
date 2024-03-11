<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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
