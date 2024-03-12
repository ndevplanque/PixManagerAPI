<?php

namespace Tests\App\Service\Photo;

use App\Entity\Album;
use App\Entity\Photo;
use App\Factory\PhotoFactory;
use App\Repository\PhotoRepository;
use App\Response\PhotoResponse;
use App\Service\Photo\PhotoCreateService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class PhotoCreateServiceTest extends TestCase
{
    private readonly PhotoCreateService $service;
    private readonly PhotoRepository $photoRepository;
    private readonly PhotoFactory $photoFactory;

    public function setUp(): void
    {
        $this->service = new PhotoCreateService(
            $this->photoRepository = $this->createMock(PhotoRepository::class),
            $this->photoFactory = $this->createMock(PhotoFactory::class),
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

        $this->assertEquals(new PhotoResponse($photo), $this->service->handle($request, $album));
    }
}
