<?php

namespace App\Response;

use JsonSerializable;

class PhotoListingByAlbumResponse implements JsonSerializable
{
    /** @param PhotoResponse[] $items */
    public function __construct(
        private readonly array $items
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'photos' => array_map(function (PhotoResponse $photoResponse) {
                return $photoResponse->jsonSerialize();
            }, $this->items)
        ];
    }
}
