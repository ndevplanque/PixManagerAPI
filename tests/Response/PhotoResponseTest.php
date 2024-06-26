<?php

namespace Tests\App\Response;

use App\Entity\Album;
use App\Entity\AppUser;
use App\Entity\Label;
use App\Entity\Photo;
use App\Response\PhotoResponse;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class PhotoResponseTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $photo = $this->createConfiguredMock(Photo::class, [
            'getId' => $id = 1,
            'getName' => $name = 'chat.jpg',
            'getAlbum' => $this->createConfiguredMock(Album::class, [
                'getId' => $albumId = 5,
                'getName' => $albumName = 'Chats',
            ]),
            'getOwner' => $this->createConfiguredMock(AppUser::class, [
                'getId' => $ownerId = 123,
            ]),
            'getLabels' => new ArrayCollection([
                $this->createConfiguredMock(Label::class, [
                    'getId' => $label1Id = 1,
                    'getName' => $label1Name = 'cats',
                ]),
                $this->createConfiguredMock(Label::class, [
                    'getId' => $label2Id = 2,
                    'getName' => $label2Name = 'cute',
                ]),
            ]),
            'getCreatedAt' => $this->createConfiguredMock(DateTimeImmutable::class, [
                'format' => $date = 'ma-date',
            ]),
        ]);

        $expected = [
            'id' => $id,
            'name' => $name,
            'album' => [
                'id' => $albumId,
                'name' => $albumName,
            ],
            'ownerId' => $ownerId,
            'labels' => [
                $label1Name,
                $label2Name,
            ],
            'createdAt' => $date,
        ];

        $this->assertEquals($expected, (new PhotoResponse($photo))->jsonSerialize());
    }
}
