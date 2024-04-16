<?php

declare(strict_types=1);

namespace App\Service\Photo;

use App\Entity\Album;
use App\Entity\Photo;
use App\Response\PhotoListingByAlbumResponse;
use App\Response\PhotoListingByUserResponse;
use App\Response\PhotoResponse;
use App\Utils\ConverterTrait;
use App\Utils\RequestHelper;
use Symfony\Component\HttpFoundation\Request;

class PhotoListingByAlbumService
{
    use ConverterTrait;

    public function __construct(
        private readonly RequestHelper $requestHelper,
    )
    {
    }

    public function handle(Request $request, Album $album): PhotoListingByAlbumResponse
    {
        $user = $this->requestHelper->getUser($request);

        $result = $user->searchPhotos(
            self::asString($this->requestHelper->getQueryParam($request, 'search')),
            self::asBool($this->requestHelper->getQueryParam($request, 'include_shared')),
            $album,
        );

        $photos = [];

        foreach ($result as $photo) {
            $photos[] = new PhotoResponse($photo);
        }

        return new PhotoListingByAlbumResponse($photos);
    }
}
