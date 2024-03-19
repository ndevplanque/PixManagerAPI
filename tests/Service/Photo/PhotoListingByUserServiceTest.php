<?php

namespace Tests\App\Service\Photo;

use App\Entity\AppUser;
use App\Entity\Photo;
use App\Response\PhotoListingByUserResponse;
use App\Response\PhotoResponse;
use App\Service\Photo\PhotoListingByUserService;
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
        $user = $this->createConfiguredMock(AppUser::class, [
            'getPhotos' => $this->createConfiguredMock(Collection::class, [
                'getValues' => [
                    $photo1 = $this->createMock(Photo::class),
                    $photo2 = $this->createMock(Photo::class),
                ]
            ])
        ]);

        $expected = new PhotoListingByUserResponse([
            new PhotoResponse($photo1),
            new PhotoResponse($photo2),
        ]);

        $this->assertEquals($expected, $this->service->handle($user));
    }
}
