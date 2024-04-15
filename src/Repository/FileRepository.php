<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Photo;
use App\Service\Compressing\CompressingPhotoService;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileRepository
{
    const PHOTO_DIR = '../resources/assets/photos/';

    private readonly ImageManager $imageManager;

    public function __construct(
        private readonly Filesystem              $filesystem,
    )
    {
        $this->imageManager = new ImageManager(Driver::class);
    }

    public function insert(Photo $photo, UploadedFile $uploaded): void
    {
        $source = $uploaded->getRealPath();
        $dest = $this->getStoragePath($photo);

        if (!file_exists($source)) {
            throw new FileNotFoundException();
        }

        if (!file_exists($dest)) {
            touch($dest, 775);
        }

        $this->imageManager
            ->read($source)
            ->save($dest, 20);
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
