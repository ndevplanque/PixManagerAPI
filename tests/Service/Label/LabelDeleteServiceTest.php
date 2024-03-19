<?php

namespace Tests\App\Service\Label;

use App\Entity\Label;
use App\Repository\LabelRepository;
use App\Service\Label\LabelDeleteService;
use PHPUnit\Framework\TestCase;

class LabelDeleteServiceTest extends TestCase
{
    private readonly LabelDeleteService $service;
    private readonly LabelRepository $labelRepository;

    public function setUp(): void
    {
        $this->service = new LabelDeleteService(
            $this->labelRepository = $this->createMock(LabelRepository::class),
        );
    }

    public function testHandle(): void
    {
        $labelName = 'a_supprimer';

        $this->labelRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => $labelName])
            ->willReturn($label = $this->createMock(Label::class));

        $this->labelRepository
            ->expects($this->once())
            ->method('delete')
            ->with($label);

        $this->service->handle($labelName);
    }
}
