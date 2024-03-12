<?php

namespace Tests\App\Service\Label;

use App\Entity\Label;
use App\Repository\LabelRepository;
use App\Service\Label\LabelDeleteService;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @throws Exception
     */
    public function testHandle(): void
    {
        $request = $this->createConfiguredMock(Request::class, [
            'toArray' => ['name' => 'mon-nouveau-label'],
        ]);

        $this->labelRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => $request->toArray()['name']])
            ->willReturn($label = $this->createMock(Label::class));

        $this->labelRepository
            ->expects($this->once())
            ->method('delete')
            ->with($label);

        $this->service->handle($request);
    }
}
