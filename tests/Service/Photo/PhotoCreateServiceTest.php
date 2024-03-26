<?php

namespace Tests\App\Service\Photo;

use App\Entity\Album;
use App\Entity\Photo;
use App\Factory\PhotoFactory;
use App\Repository\FileRepository;
use App\Repository\PhotoRepository;
use App\Response\PhotoResponse;
use App\Service\Photo\PhotoCreateService;
use App\Utils\RequestHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class PhotoCreateServiceTest extends TestCase
{
    private readonly PhotoCreateService $service;
    private readonly PhotoRepository $photoRepository;
    private readonly PhotoFactory $photoFactory;
    private readonly FileRepository $fileRepository;
    private readonly RequestHelper $requestHelper;

    public function setUp(): void
    {
        $this->service = new PhotoCreateService(
            $this->photoRepository = $this->createMock(PhotoRepository::class),
            $this->photoFactory = $this->createMock(PhotoFactory::class),
            $this->fileRepository = $this->createMock(FileRepository::class),
            $this->requestHelper = $this->createMock(RequestHelper::class),
        );
    }

    public function testHandle(): void
    {
        $request = $this->createMock(Request::class);
        $album = $this->createMock(Album::class);

        $this->photoFactory
            ->expects($this->once())
            ->method('fromRequestAndAlbum')
            ->with($request, $album)
            ->willReturn($photo = $this->createMock(Photo::class));

        $this->photoRepository
            ->expects($this->once())
            ->method('insert')
            ->with($photo)
            ->willReturn($photo);

        $this->requestHelper
            ->expects($this->once())
            ->method('getUploadedFile')
            ->with($request)
            ->willReturn($uploaded = $this->createMock(UploadedFile::class));

        $this->fileRepository
            ->expects($this->once())
            ->method('insert')
            ->with($photo, $uploaded);

        $this->assertEquals(new PhotoResponse($photo), $this->service->handle($request, $album));
    }
}
