<?php

namespace Tests\App\Service\Photo;

use App\Entity\Album;
use App\Entity\Photo;
use App\Response\PhotoListingByAlbumResponse;
use App\Response\PhotoResponse;
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
        $album = $this->createConfiguredMock(Album::class, [
            'getPhotos' => $this->createConfiguredMock(Collection::class, [
                'getValues' => [
                    $photo1 = $this->createMock(Photo::class),
                    $photo2 = $this->createMock(Photo::class),
                ]
            ])
        ]);

        $expected = new PhotoListingByAlbumResponse([
            new PhotoResponse($photo1),
            new PhotoResponse($photo2),
        ]);

        $this->assertEquals($expected, $this->service->handle($album));
    }
}
