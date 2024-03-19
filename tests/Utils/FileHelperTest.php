<?php

namespace Tests\App\Utils;

use App\Entity\AppUser;
use App\Entity\Photo;
use App\Utils\FileHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileHelperTest extends TestCase
{
    private readonly FileHelper $fileHelper;
    private readonly Filesystem $filesystem;

    public function setUp(): void
    {
        $this->fileHelper = new FileHelper(
            $this->filesystem = $this->createMock(Filesystem::class),
        );
    }

    public function testStoreUploadedPhotoFile(): void
    {
        $photo = $this->createConfiguredMock(Photo::class, [
            'getId' => 2,
            'getOwner' => $this->createConfiguredMock(AppUser::class, [
                'getId' => 1,
            ])
        ]);

        $uploaded = $this->createConfiguredMock(UploadedFile::class, [
            'getRealPath' => $tempPath = 'php-temp-file-path',
        ]);

        $this->filesystem
            ->expects($this->once())
            ->method('copy')
            ->with($tempPath, $this->fileHelper->getStoragePath($photo));

        $this->fileHelper->storeUploadedPhotoFile($photo, $uploaded);
    }

    public function testGetStoragePath(): void
    {
        $photo = $this->createConfiguredMock(Photo::class, [
            'getId' => $photoId = 2,
            'getName' => 'ma.photo.de.brigitte.' . $extension = 'jpg',
            'getOwner' => $this->createConfiguredMock(AppUser::class, [
                'getId' => $ownerId = 1,
            ])
        ]);

        $expected = FileHelper::PHOTO_DIR . "$ownerId/$photoId.$extension";

        $this->assertSame($expected, $this->fileHelper->getStoragePath($photo));
    }
}
