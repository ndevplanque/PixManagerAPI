<?php

namespace App\Service\Photo;

use App\Entity\Album;
use App\Entity\Label;

class PhotoListingByAlbumService
{
    /** @return Label[] */
    public function handle(Album $album): array
    {
        return $album->getPhotos()->getValues();
    }
}
