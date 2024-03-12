<?php

namespace Tests\App\Response;

use App\Entity\Album;
use App\Entity\Label;
use App\Entity\Photo;
use App\Response\PhotoResponse;
use DateTimeImmutable;
use DateTimeInterface;
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
            'getLabels' => $this->createConfiguredMock(Collection::class, [
                'getValues' => [
                    $this->createConfiguredMock(Label::class, [
                        'getName' => $label1 = 'chat',
                    ]),
                    $this->createConfiguredMock(Label::class, [
                        'getName' => $label2 = 'Michel',
                    ]),
                ]
            ]),
            'getCreatedAt' => $this->createConfiguredMock(DateTimeImmutable::class, [
                'format' => $date = 'ma-date',
            ]),
        ]);

        $response = new PhotoResponse($photo);

        $expected = [
            'id' => $id,
            'name' => $name,
            'album' => [
                'id' => $albumId,
                'name' => $albumName,
            ],
            'labels' => [$label1, $label2],
            'createdAt' => $date,
        ];

        $this->assertEquals($expected, $response->jsonSerialize());
    }
}
