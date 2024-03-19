<?php

declare(strict_types=1);

namespace App\Response;

use App\Entity\Label;
use JsonSerializable;

class LabelResponse implements JsonSerializable
{
    public function __construct(
        private readonly Label $label
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->label->getId(),
            'name' => $this->label->getName(),
        ];
    }

}
