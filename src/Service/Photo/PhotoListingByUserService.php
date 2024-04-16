<?php

declare(strict_types=1);

namespace App\Service\Photo;

use App\Response\PhotoListingByUserResponse;
use App\Response\PhotoResponse;
use App\Utils\ConverterTrait;
use App\Utils\RequestHelper;
use Symfony\Component\HttpFoundation\Request;

class PhotoListingByUserService
{
    use ConverterTrait;

    public function __construct(
        private readonly RequestHelper $requestHelper,
    )
    {
    }

    public function handle(Request $request): PhotoListingByUserResponse
    {
        $user = $this->requestHelper->getUser($request);

        $result = $user->searchPhotos(
            self::asString($this->requestHelper->getQueryParam($request, 'search')),
            self::asBool($this->requestHelper->getQueryParam($request, 'include_shared')),
        );

        $photos = [];

        foreach ($result as $photo) {
            $photos[] = new PhotoResponse($photo);
        }

        return new PhotoListingByUserResponse($photos);
    }
}
