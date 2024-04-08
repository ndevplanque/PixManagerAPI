<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Photo;
use App\Service\Compressing\CompressingPhotoService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileRepository
{
    const PHOTO_DIR = 'assets/photos/';

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly CompressingPhotoService $compressingPhotoService
    )
    {
    }

    public function insert(Photo $photo, UploadedFile $uploaded): void
    {
        $pathToPhoto = $uploaded->getRealPath();
        $pathToSave = $this->getStoragePath($photo);

        $this->compressingPhotoService->compressingPhoto($pathToPhoto, $pathToSave);
    }

    public function delete(Photo $photo): void
    {
        $this->filesystem->remove($this->getStoragePath($photo));
    }

    public function getStoragePath(Photo $photo): string
    {
        $ownerId = $photo->getOwner()->getId();
        $photoId = $photo->getId();

        $tmp = explode('.', $photo->getName());
        $extension = $tmp[count($tmp) - 1];

        return self::PHOTO_DIR . "$ownerId/$photoId.$extension";
    }

    //public function sendAvatar(AppUser $user): BinaryFileResponse
    //public function sendJacket(Album $album): BinaryFileResponse
}
