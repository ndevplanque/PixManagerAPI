<?php

namespace Tests\App\Factory;

use App\Entity\Album;
use App\Entity\AppUser;
use App\Entity\Label;
use App\Entity\Photo;
use App\Factory\PhotoFactory;
use App\Repository\LabelRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class PhotoFactoryTest extends TestCase
{
    private readonly PhotoFactory $factory;
    private readonly LabelRepository $labelRepository;

    public function setUp(): void
    {
        $this->factory = new PhotoFactory(
            $this->labelRepository = $this->createMock(LabelRepository::class),
        );
    }

    public function testFromRequest(): void
    {
        $this->markTestIncomplete('Owner should be found from JWT in request');

        $request = $this->createConfiguredMock(Request::class, [
            'toArray' => $payload = [
                'name' => 'macron-et-son-chien.jpg',
                'labels' => ['macron', 'chien'],
            ],
        ]);

        $album = $this->createMock(Album::class);

        $owner = $this->createConfiguredMock(AppUser::class, [
            'newPhoto' => $photo = new Photo('chien.jpg', $album),
        ]);

        $this->labelRepository
            ->expects($this->exactly(2))
            ->method('findOrInsert')
            ->willReturnMap([
                ['macron', $labelMacron = $this->createMock(Label::class)],
                ['chien', $labelChien = $this->createMock(Label::class)],
            ]);

        $photo->addLabel($labelChien);
        $photo->addLabel($labelMacron);

        $this->assertEquals($photo, $this->factory->fromRequestAndAlbum($request, $album));
    }
}
