<?php

namespace Tests\App\Service\Label;

use App\Entity\Label;
use App\Factory\LabelFactory;
use App\Repository\LabelRepository;
use App\Service\Label\LabelCreateService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class LabelCreateServiceTest extends TestCase
{
    private readonly LabelCreateService $service;
    private readonly LabelRepository $labelRepository;
    private readonly LabelFactory $labelFactory;

    public function setUp(): void
    {
        $this->service = new LabelCreateService(
            $this->labelRepository = $this->createMock(LabelRepository::class),
            $this->labelFactory = $this->createMock(LabelFactory::class),
        );
    }

    public function testHandle(): void
    {
        $request = $this->createMock(Request::class);

        $this->labelFactory
            ->expects($this->once())
            ->method('fromRequest')
            ->with($request)
            ->willReturn($label = $this->createMock(Label::class));

        $this->labelRepository
            ->expects($this->once())
            ->method('insert')
            ->with($label);

        $this->service->handle($request);
    }
}
