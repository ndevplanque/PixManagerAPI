<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\AppUser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class RequestHelper
{
    public function getQueryParam(Request $request, string $key): bool|float|int|null|string
    {
        return $request->query->get($key);
    }

    public function getBodyParam(Request $request, string $key): bool|float|int|null|string
    {
        return $request->request->get($key);
    }

    public function getUploadedFile(Request $request, string $key): UploadedFile
    {
        return $request->files->get($key);
    }

    public function getAttribute(Request $request, string $key): bool|float|int|null|string
    {
        return $request->attributes->get($key);
    }

    public function getUser(Request $request): AppUser
    {
        return $request->attributes->get('jwt-token-owner');
    }
}
