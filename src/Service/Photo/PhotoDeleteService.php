<?php

namespace App\Service\Photo;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Exception;

class PhotoDeleteService
{
    private readonly PhotoRepository $photoRepository;

    public function __construct(PhotoRepository $photoRepository)
    {
        $this->photoRepository = $photoRepository;
    }

    /**
     * @throws Exception
     */
    public function handle(Photo $photo): void
    {
        $this->photoRepository->delete($photo);
    }
}
