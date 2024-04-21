<?php

namespace Tests\App\Service\Photo;

use App\Entity\Album;
use App\Entity\AppUser;
use App\Entity\Photo;
use App\Response\PhotoListingByAlbumResponse;
use App\Response\PhotoResponse;
use App\Service\Photo\PhotoListingByAlbumService;
use App\Utils\RequestHelper;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class PhotoListingByAlbumServiceTest extends TestCase
{
    private readonly PhotoListingByAlbumService $service;
    private readonly RequestHelper $requestHelper;

    public function setUp(): void
    {
        $this->service = new PhotoListingByAlbumService(
            $this->requestHelper = $this->createMock(RequestHelper::class),
        );
    }

    public function testHandle(): void
    {
        $request = $this->createMock(Request::class);

        $user = $this->createConfiguredMock(AppUser::class, [
            'searchPhotos' => new ArrayCollection([
                $photo1 = $this->createMock(Photo::class),
                $photo2 = $this->createMock(Photo::class),
            ])
        ]);

        $this->requestHelper
            ->expects($this->once())
            ->method('getUser')
            ->with($request)
            ->willReturn($user);

        $album = $this->createMock(Album::class);

        $expected = new PhotoListingByAlbumResponse([
            new PhotoResponse($photo1),
            new PhotoResponse($photo2),
        ]);

        $this->assertEquals($expected, $this->service->handle($request, $album));
    }
}
