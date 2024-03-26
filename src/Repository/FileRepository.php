<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Photo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileRepository
{
    const PHOTO_DIR = 'assets/photos/';

    public function __construct(private readonly Filesystem $filesystem)
    {
    }

    public function insert(Photo $photo, UploadedFile $uploaded): void
    {
        $this->filesystem->copy($uploaded->getRealPath(), $this->getStoragePath($photo));
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
