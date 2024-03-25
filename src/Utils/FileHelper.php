<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\Photo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;

class FileHelper
{
    const PHOTO_DIR = 'assets/photos/';

    public function __construct(private readonly Filesystem $filesystem)
    {
    }

    public function storeUploadedPhotoFile(Photo $photo, UploadedFile $uploaded): void
    {
        $this->filesystem->copy($uploaded->getRealPath(), $this->getStoragePath($photo));
    }

    public function sendPhoto(Photo $photo): BinaryFileResponse
    {
        return new BinaryFileResponse(
            file: $this->getStoragePath($photo),
            headers: ['Content-Disposition' => HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                $photo->getName(),
            )]
        );
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
