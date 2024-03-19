<?php

namespace Tests\App\Response;

use App\Entity\Album;
use App\Entity\AppUser;
use App\Entity\Label;
use App\Entity\Photo;
use App\Response\PhotoResponse;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
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
            'getLabels' => $this->createConfiguredMock(Collection::class, [
                'getValues' => [
                    $this->createConfiguredMock(Label::class, [
                        'getId' => $label1Id = 1,
                        'getName' => $label1Name = 'chat',
                    ]),
                    $this->createConfiguredMock(Label::class, [
                        'getId' => $label2Id = 2,
                        'getName' => $label2Name = 'Michel',
                    ]),
                ]
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
                ['id' => $label1Id, 'name' => $label1Name],
                ['id' => $label2Id, 'name' => $label2Name],
            ],
            'createdAt' => $date,
        ];

        $this->assertEquals($expected, (new PhotoResponse($photo))->jsonSerialize());
    }
}
