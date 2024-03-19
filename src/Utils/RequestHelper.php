<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class RequestHelper
{
    public function getParameter(Request $request, string $key): bool|float|int|null|string
    {
        return $request->request->get($key);
    }

    public function getUploadedFile(Request $request, string $key): UploadedFile
    {
        return $request->files->get($key);
    }
}
