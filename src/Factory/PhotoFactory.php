<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Album;
use App\Entity\Photo;
use App\Repository\LabelRepository;
use App\Utils\RequestHelper;
use Symfony\Component\HttpFoundation\Request;

class PhotoFactory
{
    public function __construct(
        private readonly LabelRepository $labelRepository,
        private readonly RequestHelper   $requestHelper,
    )
    {
    }

    public function fromRequestAndAlbum(Request $request, Album $album): Photo
    {
        $json = $this->requestHelper->getBodyParam($request, 'labels');

        $labels = $json !== null ? json_decode($json) : [];

        $file = $this->requestHelper->getUploadedFile($request);

        $photo = $this->requestHelper->getUser($request)
            ->newPhoto($file->getClientOriginalName(), $album);

        for ($i = 0; $i < count($labels); $i++) {
            $photo->addLabel(
                $this->labelRepository->findOrInsert($labels[$i])
            );
        }

        return $photo;
    }
}
