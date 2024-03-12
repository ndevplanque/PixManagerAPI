<?php

namespace App\Response;

use App\Entity\Label;
use JsonSerializable;

class LabelListingResponse implements JsonSerializable
{
    /** @var Label[] $items */
    private array $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function jsonSerialize(): array
    {
        return array_map(fn(Label $label) => $label->getName(), $this->items);
    }

}
