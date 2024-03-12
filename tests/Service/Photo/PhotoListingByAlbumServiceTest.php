<?php

namespace Tests\App\Service\Photo;

use App\Entity\Album;
use App\Entity\Photo;
use App\Service\Photo\PhotoListingByAlbumService;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class PhotoListingByAlbumServiceTest extends TestCase
{
    private readonly PhotoListingByAlbumService $service;

    public function setUp(): void
    {
        $this->service = new PhotoListingByAlbumService();
    }

    public function testHandle(): void
    {
        $album = $this->createMock(Album::class);

        $album
            ->expects($this->once())
            ->method('getPhotos')
            ->willReturn($collection = $this->createMock(Collection::class));

        $collection
            ->expects($this->once())
            ->method('getValues')
            ->willReturn($photos = [
                $this->createMock(Photo::class),
                $this->createMock(Photo::class),
            ]);

        $this->assertSame($photos, $this->service->handle($album));
    }
}
