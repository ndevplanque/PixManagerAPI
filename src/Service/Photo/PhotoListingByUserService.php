<?php

declare(strict_types=1);

namespace App\Service\Photo;

use App\Entity\AppUser;
use App\Entity\Photo;
use App\Response\PhotoListingByUserResponse;
use App\Response\PhotoResponse;

class PhotoListingByUserService
{
    public function handle(AppUser $user, ?string $search = null): PhotoListingByUserResponse
    {
        if ($search === null) {
            $photos = [];

            foreach ($user->getPhotos()->getIterator() as $photo) {
                $photos[] = new PhotoResponse($photo);
            }

            return new PhotoListingByUserResponse($photos);
        }

        $photosById = [];
        $accuracyById = [];

        foreach ($user->getPhotos()->getIterator() as $photo) {
            $photosById[$photo->getId()] = $photo;
            $accuracyById[$photo->getId()] = $photo->getAccuracyScore($search);
        }

        // Sort array by ascending values (because accuracyScore = the least, the most accurate)
        asort($accuracyById);

        $idsByAccury = array_keys($accuracyById);

        $photosByAccuracy = [];

        foreach ($idsByAccury as $id) {
            $photosByAccuracy[] = new PhotoResponse($photosById[$id]);
        }

        return new PhotoListingByUserResponse($photosByAccuracy);
    }
}
