<?php

namespace Tests\App\Factory;

use App\Entity\Label;
use App\Factory\PhotoFactory;
use App\Repository\LabelRepository;
use App\Utils\RequestHelper;
use PHPUnit\Framework\TestCase;

class PhotoFactoryTest extends TestCase
{
    private readonly PhotoFactory $factory;
    private readonly LabelRepository $labelRepository;
    private readonly RequestHelper $requestHelper;

    public function setUp(): void
    {
        $this->factory = new PhotoFactory(
            $this->labelRepository = $this->createMock(LabelRepository::class),
            $this->requestHelper = $this->createMock(RequestHelper::class),
        );
    }

    public function testFromRequest(): void
    {
        $this->markTestIncomplete('Owner should be found from JWT in request');

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
