<?php

namespace App\Response;

use App\Entity\Label;
use JsonSerializable;

class LabelListingResponse implements JsonSerializable
{
    /** @param Label[] $items */
    public function __construct(
        private readonly array $items
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'labels' => array_map(
                fn(Label $label) => (new LabelResponse($label))->jsonSerialize(),
                $this->items
            ),
        ];
    }
}
