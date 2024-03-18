<?php

namespace Tests\App\Service\Photo;

use App\Entity\Album;
use App\Entity\AppUser;
use App\Entity\Label;
use App\Entity\Photo;
use App\Repository\AlbumRepository;
use App\Repository\LabelRepository;
use App\Repository\PhotoRepository;
use App\Service\Photo\PhotoUpdateService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PhotoUpdateServiceTest extends TestCase
{
    private readonly PhotoUpdateService $service;
    private readonly PhotoRepository $photoRepository;
    private readonly AlbumRepository $albumRepository;
    private readonly LabelRepository $labelRepository;

    public function setUp(): void
    {
        $this->service = new PhotoUpdateService(
            $this->photoRepository = $this->createMock(PhotoRepository::class),
            $this->albumRepository = $this->createMock(AlbumRepository::class),
            $this->labelRepository = $this->createMock(LabelRepository::class),
        );
    }

    public function testHandleShouldThrowWhenAlbumIsNotFound(): void
    {
        $request = $this->createConfiguredMock(Request::class, [
            'toArray' => ['albumId' => $albumId = 123],
        ]);

        $photo = $this->createMock(Photo::class);

        $this->albumRepository
            ->expects($this->once())
            ->method('find')
            ->with($albumId)
            ->willReturn(null);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("Album #$albumId not found!");

        $this->service->handle($request, $photo);
    }

    public function testHandleShouldThrowWhenAlbumBelongsToSomeoneElse(): void
    {
        $request = $this->createConfiguredMock(Request::class, [
            'toArray' => ['albumId' => $albumId = 123],
        ]);

        $photo = $this->createConfiguredMock(Photo::class, [
            'getAlbum' => $this->createConfiguredMock(Album::class, [
                'getOwner' => $this->createMock(AppUser::class),
            ])
        ]);

        $this->albumRepository
            ->expects($this->once())
            ->method('find')
            ->with($albumId)
            ->willReturn($this->createConfiguredMock(Album::class, [
                'getOwner' => $this->createMock(AppUser::class),
            ]));

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("Album #$albumId belongs to someone else!");

        $this->service->handle($request, $photo);
    }

    public function testHandle(): void
    {
        $request = $this->createConfiguredMock(Request::class, [
            'toArray' => [
                'albumId' => $albumId = 123,
                'addLabels' => $addLabels = ['chien', 'chat'],
                'removeLabels' => $removeLabels = ['soleil', 'montagne', 'paysage'],
                'name' => $name = 'chien et chat au clair de lune',
            ],
        ]);

        $photo = $this->createConfiguredMock(Photo::class, [
            'getAlbum' => $this->createConfiguredMock(Album::class, [
                'getOwner' => $user = $this->createMock(AppUser::class)
            ])
        ]);

        $photo
            ->expects($this->once())
            ->method('setName')
            ->with($name)
            ->willReturnSelf();


        $this->labelRepository
            ->expects($this->exactly(2))
            ->method('findOrInsert')
            ->willReturnMap([
                [$addLabels[0], $chien = $this->createMock(Label::class)],
                [$addLabels[1], $chat = $this->createMock(Label::class)],
            ]);

        $photo
            ->expects($this->exactly(2))
            ->method('addLabel')
            ->willReturnMap([
                [$chien, $photo],
                [$chat, $photo],
            ]);

        $photo
            ->expects($this->once())
            ->method('removeLabelsByName')
            ->with($removeLabels)
            ->willReturnSelf();

        $this->albumRepository
            ->expects($this->once())
            ->method('find')
            ->with($albumId)
            ->willReturn($targetAlbum = $this->createConfiguredMock(Album::class, [
                'getOwner' => $user,
            ]));

        $photo
            ->expects($this->once())
            ->method('setAlbum')
            ->with($targetAlbum)
            ->willReturnSelf();

        $this->photoRepository
            ->expects($this->once())
            ->method('update')
            ->with($photo)
            ->willReturn($photo);

        $this->assertSame($photo, $this->service->handle($request, $photo));
    }
}
