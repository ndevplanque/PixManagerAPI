<?php

namespace App\Service\Photo;

use App\Entity\Album;
use App\Entity\Photo;
use App\Factory\PhotoFactory;
use App\Repository\PhotoRepository;
use Symfony\Component\HttpFoundation\Request;

class PhotoCreateService
{
    private readonly PhotoRepository $photoRepository;
    private readonly PhotoFactory $photoFactory;

    public function __construct(
        PhotoRepository $photoRepository,
        PhotoFactory    $photoFactory,
    )
    {
        $this->photoRepository = $photoRepository;
        $this->photoFactory = $photoFactory;
    }

    public function handle(Request $request, Album $album): Photo
    {
        $photo = $this->photoFactory->fromRequestAndAlbum($request, $album);

        return $this->photoRepository->insert($photo);
    }
}
