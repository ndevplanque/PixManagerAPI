<?php

declare(strict_types=1);

namespace App\Service\Photo;

use App\Entity\Photo;
use App\Repository\FileRepository;
use App\Repository\PhotoRepository;

class PhotoDeleteService
{
    public function __construct(
        private readonly PhotoRepository $photoRepository,
        private readonly FileRepository  $fileRepository,
    )
    {
    }

    public function handle(Photo $photo): void
    {
        $this->photoRepository->delete($photo);
        $this->fileRepository->delete($photo);
    }
}
