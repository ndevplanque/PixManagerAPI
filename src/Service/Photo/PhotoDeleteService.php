<?php

declare(strict_types=1);

namespace App\Service\Photo;

use App\Entity\Photo;
use App\Repository\PhotoRepository;

class PhotoDeleteService
{
    public function __construct(
        private readonly PhotoRepository $photoRepository,
    )
    {
    }

    public function handle(Photo $photo): void
    {
        $this->photoRepository->delete($photo);
    }
}
