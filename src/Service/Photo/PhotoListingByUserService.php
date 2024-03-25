<?php

declare(strict_types=1);

namespace App\Service\Photo;

use App\Entity\AppUser;
use App\Entity\Photo;
use App\Response\PhotoListingByUserResponse;
use App\Response\PhotoResponse;

class PhotoListingByUserService
{
    public function handle(AppUser $user): PhotoListingByUserResponse
    {
        return new PhotoListingByUserResponse(
            array_map(
                fn(Photo $photo) => new PhotoResponse($photo),
                $user->getPhotos()->getValues()
            )
        );
    }
}
