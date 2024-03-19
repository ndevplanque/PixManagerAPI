<?php

namespace App\Service\Photo;

use App\Entity\Label;
use App\Entity\Photo;
use App\Response\PhotoListingByLabelResponse;
use App\Response\PhotoResponse;
use Exception;

class PhotoListingByLabelService
{
    /**
     * @throws Exception
     */
    public function handle(Label $label): PhotoListingByLabelResponse
    {
        // todo: get user from jwt instead
        $user = $label->getPhotos()->first()->getOwner();

        $photos = [];

        foreach ($user->getOwnedAlbums()->getIterator() as $album) {
            foreach ($album->getPhotos()->getIterator() as $photo) {
                if ($photo->getLabels()->contains($label)) {
                    $photos[] = $photo;
                }
            }
        }

        foreach ($user->getSharedAlbums()->getIterator() as $album) {
            foreach ($album->getPhotos()->getIterator() as $photo) {
                if ($photo->getLabels()->contains($label)) {
                    $photos[] = $photo;
                }
            }
        }

        return new PhotoListingByLabelResponse(
            array_map(fn(Photo $photo) => new PhotoResponse($photo), $photos)
        );
    }
}
