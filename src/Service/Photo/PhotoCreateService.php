<?php

namespace App\Service\Photo;

use App\Entity\Album;
use App\Factory\PhotoFactory;
use App\Repository\PhotoRepository;
use App\Response\PhotoResponse;
use App\Utils\FileHelper;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class PhotoCreateService
{
    public function __construct(
        private readonly PhotoRepository $photoRepository,
        private readonly PhotoFactory    $photoFactory,
        private readonly FileHelper      $fileHelper,
    )
    {
    }

    public function handle(Request $request, Album $album): PhotoResponse
    {
        $photo = $this->photoRepository->insert(
            $this->photoFactory->fromRequestAndAlbum($request, $album)
        );

        /** @var UploadedFile $uploaded */
        $uploaded = $request->files->get('file');

        $this->fileHelper->storeUploadedPhotoFile($photo, $uploaded);

        return new PhotoResponse($photo);
    }
}
