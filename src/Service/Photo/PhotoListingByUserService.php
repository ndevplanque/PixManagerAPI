<?php

declare(strict_types=1);

namespace App\Service\Photo;

use App\Response\PhotoListingByUserResponse;
use App\Response\PhotoResponse;
use App\Utils\RequestHelper;
use Symfony\Component\HttpFoundation\Request;

class PhotoListingByUserService
{
    public function __construct(
        private readonly RequestHelper $requestHelper,
    )
    {
    }

    public function handle(Request $request): PhotoListingByUserResponse
    {
        $user = $this->requestHelper->getUser($request);

        $search = $this->requestHelper->getQueryParam($request, 'search');
        $search = $search !== null ? (string)$search : null;

        $includeShared = $this->requestHelper->getQueryParam($request, 'include_shared');
        $includeShared = $includeShared !== null ? (bool)$includeShared : null;

        $collection = $includeShared ? $user->getAllPhotos() : $user->getOwnedPhotos();

        if ($search === null) {
            $photos = [];

            foreach ($collection as $photo) {
                $photos[] = new PhotoResponse($photo);
            }

            return new PhotoListingByUserResponse($photos);
        }

        $photosById = [];
        $accuracyById = [];

        foreach ($collection as $photo) {
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
