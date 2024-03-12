<?php

namespace App\Service\Photo;

use App\Entity\Album;
use App\Factory\PhotoFactory;
use App\Repository\PhotoRepository;
use App\Response\PhotoResponse;
use Symfony\Component\HttpFoundation\Request;

class PhotoCreateService
{
    public function __construct(
        private readonly PhotoRepository $photoRepository,
        private readonly PhotoFactory    $photoFactory,
    )
    {
    }

    public function handle(Request $request, Album $album): PhotoResponse
    {
        $photo = $this->photoRepository->insert(
            $this->photoFactory->fromRequestAndAlbum($request, $album)
        );

        return new PhotoResponse($photo);
    }
}
