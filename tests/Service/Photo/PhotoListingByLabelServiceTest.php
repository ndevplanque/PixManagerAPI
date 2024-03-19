<?php

namespace Tests\App\Service\Photo;

use App\Repository\LabelRepository;
use App\Service\Photo\PhotoListingByLabelService;
use PHPUnit\Framework\TestCase;

class PhotoListingByLabelServiceTest extends TestCase
{
    private readonly PhotoListingByLabelService $service;
    private readonly LabelRepository $labelRepository;

    public function setUp(): void
    {
        $this->service = new PhotoListingByLabelService(
            $this->labelRepository = $this->createMock(LabelRepository::class),
        );
    }

    public function testHandle(): void
    {
        $this->markTestIncomplete('User should be found from JWT in request');
    }
}
