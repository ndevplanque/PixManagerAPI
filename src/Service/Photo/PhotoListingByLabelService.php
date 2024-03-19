<?php

declare(strict_types=1);

namespace App\Service\Photo;

use App\Entity\Photo;
use App\Repository\LabelRepository;
use App\Response\PhotoListingByLabelResponse;
use App\Response\PhotoResponse;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PhotoListingByLabelService
{
    public function __construct(
        private readonly LabelRepository $labelRepository
    )
    {
    }

    /**
     * @throws Exception
     */
    public function handle(string $labelName): PhotoListingByLabelResponse
    {
        $label = $this->labelRepository->findOneBy(['name' => $labelName]);

        if ($label === null) {
            throw new HttpException(404, "Label $labelName not found!");
        }

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
