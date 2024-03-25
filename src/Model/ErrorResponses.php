<?php

namespace App\Model;

use OpenApi\Attributes as OA;

class ErrorResponses
{
    public static function notFound(): OA\Response
    {
        return new OA\Response(
            response: 404,
            description: 'Not found',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "success", type: "boolean", example: false),
                    new OA\Property(property: "status", type: "integer", example: 404),
                    new OA\Property(property: "message", type: "string", example: "Not Found"),
                    new OA\Property(property: "reason", type: "string", example: "Resource not found"),
                ],
                type: 'object'
            )
        );
    }
}