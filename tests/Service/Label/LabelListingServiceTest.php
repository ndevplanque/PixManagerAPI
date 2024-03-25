<?php

namespace Tests\App\Service\Label;

use App\Entity\Label;
use App\Repository\LabelRepository;
use App\Response\LabelListingResponse;
use App\Service\Label\LabelListingService;
use PHPUnit\Framework\TestCase;

class LabelListingServiceTest extends TestCase
{
    private readonly LabelListingService $service;
    private readonly LabelRepository $labelRepository;

    public function setUp(): void
    {
        $this->service = new LabelListingService(
            $this->labelRepository = $this->createMock(LabelRepository::class),
        );
    }

    public function testHandle(): void
    {

        $this->labelRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($labels = [
                $this->createMock(Label::class),
                $this->createMock(Label::class),
            ]);

        $expectedResponse = new LabelListingResponse($labels);

        $this->assertEquals($expectedResponse, $this->service->handle());
    }
}
