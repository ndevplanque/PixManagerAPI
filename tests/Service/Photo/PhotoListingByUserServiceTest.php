<?php

namespace Tests\App\Service\Photo;

use App\Entity\Album;
use App\Entity\AppUser;
use App\Entity\Photo;
use App\Service\Photo\PhotoListingByUserService;
use ArrayIterator;
use Doctrine\Common\Collections\Collection;
use Exception;
use PHPUnit\Framework\TestCase;

class PhotoListingByUserServiceTest extends TestCase
{
    private readonly PhotoListingByUserService $service;

    public function setUp(): void
    {
        $this->service = new PhotoListingByUserService();
    }

    /**
     * @throws Exception
     */
    public function testHandle(): void
    {
        $user = $this->createMock(AppUser::class);

        $user
            ->expects($this->once())
            ->method('getOwnedAlbums')
            ->willReturn($albumCollection = $this->createMock(Collection::class));

        $albumCollection
            ->expects($this->once())
            ->method('getIterator')
            ->willReturn(new ArrayIterator([
                $album1 = $this->createMock(Album::class),
                $album2 = $this->createMock(Album::class),
            ]));

        $album1
            ->expects($this->once())
            ->method('getPhotos')
            ->willReturn($photoCollection1 = $this->createMock(Collection::class));

        $photoCollection1
            ->expects($this->once())
            ->method('getValues')
            ->willReturn([
                $photo1 = $this->createMock(Photo::class),
                $photo2 = $this->createMock(Photo::class),
            ]);

        $album2
            ->expects($this->once())
            ->method('getPhotos')
            ->willReturn($photoCollection2 = $this->createMock(Collection::class));

        $photoCollection2
            ->expects($this->once())
            ->method('getValues')
            ->willReturn([
                $photo3 = $this->createMock(Photo::class),
                $photo4 = $this->createMock(Photo::class),
            ]);

        $this->assertSame([$photo1, $photo2, $photo3, $photo4], $this->service->handle($user));
    }
}
