<?php

namespace App\Service\Photo;

use App\Entity\Album;
use App\Entity\Photo;
use App\Response\PhotoListingByAlbumResponse;
use App\Response\PhotoResponse;

class PhotoListingByAlbumService
{
    public function handle(Album $album): PhotoListingByAlbumResponse
    {
        return new PhotoListingByAlbumResponse(
            array_map(
                fn(Photo $photo) => new PhotoResponse($photo),
                $album->getPhotos()->getValues()
            )
        );
    }
}
