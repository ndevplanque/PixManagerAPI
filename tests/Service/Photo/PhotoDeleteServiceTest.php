<?php

namespace Tests\App\Service\Photo;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use App\Service\Photo\PhotoDeleteService;
use Exception;
use PHPUnit\Framework\TestCase;

class PhotoDeleteServiceTest extends TestCase
{
    private readonly PhotoDeleteService $service;
    private readonly PhotoRepository $photoRepository;

    public function setUp(): void
    {
        $this->service = new PhotoDeleteService(
            $this->photoRepository = $this->createMock(PhotoRepository::class),
        );
    }

    /**
     * @throws Exception
     */
    public function testHandle(): void
    {
        $photo = $this->createMock(Photo::class);

        $this->photoRepository
            ->expects($this->once())
            ->method('delete')
            ->with($photo);

        $this->service->handle($photo);
    }
}
