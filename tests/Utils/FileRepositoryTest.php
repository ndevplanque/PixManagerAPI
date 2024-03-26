<?php

namespace Tests\App\Utils;

use App\Entity\AppUser;
use App\Entity\Photo;
use App\Repository\FileRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileRepositoryTest extends TestCase
{
    private readonly FileRepository $fileRepository;
    private readonly Filesystem $filesystem;

    public function setUp(): void
    {
        $this->fileRepository = new FileRepository(
            $this->filesystem = $this->createMock(Filesystem::class),
        );
    }

    public function testInsert(): void
    {
        $photo = $this->createConfiguredMock(Photo::class, [
            'getId' => 2,
            'getName' => 'ma photo.jpg',
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
            ->with($tempPath, $this->fileRepository->getStoragePath($photo));

        $this->fileRepository->insert($photo, $uploaded);
    }


    public function testDelete(): void
    {
        $photo = $this->createConfiguredMock(Photo::class, [
            'getId' => 2,
            'getName' => 'ma photo.jpg',
            'getOwner' => $this->createConfiguredMock(AppUser::class, [
                'getId' => 1,
            ])
        ]);

        $this->filesystem
            ->expects($this->once())
            ->method('remove')
            ->with($this->fileRepository->getStoragePath($photo));

        $this->fileRepository->delete($photo);
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

        $expected = FileRepository::PHOTO_DIR . "$ownerId/$photoId.$extension";

        $this->assertSame($expected, $this->fileRepository->getStoragePath($photo));
    }
}
