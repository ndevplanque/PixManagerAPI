<?php

namespace App\Service\Photo;

use App\Entity\Album;
use App\Entity\AppUser;
use App\Entity\Photo;
use Exception;

class PhotoListingByUserService
{
    /**
     * @return Photo[]
     * @throws Exception
     */
    public function handle(AppUser $user): array
    {
        $albums = $user->getOwnedAlbums()->getIterator();
        $photos = [];

        foreach ($albums as $album) {
            /** @var Album $album */
            $photos[] = $album->getPhotos()->getValues();
        }

        return $photos;
    }
}
