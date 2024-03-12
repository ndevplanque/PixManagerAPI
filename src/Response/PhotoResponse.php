<?php

namespace App\Response;

use App\Entity\Photo;
use DateTimeInterface;
use JsonSerializable;

class PhotoResponse implements JsonSerializable
{
    public function __construct(
        private readonly Photo $photo
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->photo->getId(),
            'name' => $this->photo->getName(),
            'album' => [
                'id' => $this->photo->getAlbum()->getId(),
                'name' => $this->photo->getAlbum()->getName(),
            ],
            'labels' => new LabelListingResponse($this->photo->getLabels()->getValues()),
            'createdAt' => $this->photo->getCreatedAt()->format(DateTimeInterface::RFC3339),
        ];
    }
}
