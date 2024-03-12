<?php

namespace App\Service\Photo;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Exception;

class PhotoDeleteService
{
    public function __construct(
        private readonly PhotoRepository $photoRepository,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function handle(Photo $photo): void
    {
        $this->photoRepository->delete($photo);
    }
}
