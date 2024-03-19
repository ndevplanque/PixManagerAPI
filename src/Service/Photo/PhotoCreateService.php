<?php

namespace App\Service\Photo;

use App\Entity\Album;
use App\Factory\PhotoFactory;
use App\Repository\PhotoRepository;
use App\Response\PhotoResponse;
use App\Utils\FileHelper;
use App\Utils\RequestHelper;
use Symfony\Component\HttpFoundation\Request;

class PhotoCreateService
{
    public function __construct(
        private readonly PhotoRepository $photoRepository,
        private readonly PhotoFactory    $photoFactory,
        private readonly FileHelper      $fileHelper,
        private readonly RequestHelper   $requestHelper,
    )
    {
    }

    public function handle(Request $request, Album $album): PhotoResponse
    {
        $photo = $this->photoRepository->insert(
            $this->photoFactory->fromRequestAndAlbum($request, $album)
        );

        $this->fileHelper->storeUploadedPhotoFile(
            $photo,
            $this->requestHelper->getUploadedFile($request, 'file')
        );

        return new PhotoResponse($photo);
    }
}
