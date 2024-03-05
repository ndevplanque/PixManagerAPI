<?php

namespace App\Services;

use App\Entity\Photo;
use App\Repository\PhotoRepository;

class PhotoService
{
    public function __construct(
        public readonly PhotoRepository $repository,
    )
    {
    }

    /** @return Photo[] */
    public function list(): array
    {
        return $this->repository->findAll();
    }
}
