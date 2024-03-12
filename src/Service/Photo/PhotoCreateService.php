<?php

namespace App\Service\Photo;

use App\Entity\Album;
use App\Entity\Photo;
use App\Factory\PhotoFactory;
use App\Repository\PhotoRepository;
use Symfony\Component\HttpFoundation\Request;

class PhotoCreateService
{
    private readonly PhotoFactory $photoFactory;
    private readonly PhotoRepository $photoRepository;

    public function __construct(
        PhotoFactory    $photoFactory,
        PhotoRepository $photoRepository,
    )
    {
        $this->photoFactory = $photoFactory;
        $this->photoRepository = $photoRepository;
    }

    public function handle(Request $request, Album $album): Photo
    {
        $photo = $this->photoFactory->fromRequestAndAlbum($request, $album);

        return $this->photoRepository->insert($photo);
    }
}
