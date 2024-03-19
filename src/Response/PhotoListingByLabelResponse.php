<?php

declare(strict_types=1);

namespace App\Response;

use JsonSerializable;

class PhotoListingByLabelResponse implements JsonSerializable
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
