<?php

namespace Tests\App\Response;

use App\Response\PhotoListingByUserResponse;
use App\Response\PhotoResponse;
use PHPUnit\Framework\TestCase;

class PhotoListingByUserResponseTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $response = new PhotoListingByUserResponse([
            $photo1 = $this->createMock(PhotoResponse::class),
            $photo2 = $this->createMock(PhotoResponse::class),
            $photo3 = $this->createMock(PhotoResponse::class),
        ]);

        $photo1
            ->expects($this->once())
            ->method('jsonSerialize')
            ->willReturn($photo1Data = ['name' => 'photo1']);

        $photo2
            ->expects($this->once())
            ->method('jsonSerialize')
            ->willReturn($photo2Data = ['name' => 'photo2']);

        $photo3
            ->expects($this->once())
            ->method('jsonSerialize')
            ->willReturn($photo3Data = ['name' => 'photo3']);

        $expected = ['photos' => [
            $photo1Data,
            $photo2Data,
            $photo3Data,
        ]];

        $this->assertEquals($expected, $response->jsonSerialize());
    }
}
