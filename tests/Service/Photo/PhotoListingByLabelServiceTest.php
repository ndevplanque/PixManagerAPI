<?php

namespace Tests\App\Service\Photo;

use App\Service\Photo\PhotoListingByLabelService;
use Exception;
use PHPUnit\Framework\TestCase;

class PhotoListingByLabelServiceTest extends TestCase
{
    private readonly PhotoListingByLabelService $service;

    public function setUp(): void
    {
        $this->service = new PhotoListingByLabelService();
    }

    /**
     * @throws Exception
     */
    public function testHandle(): void
    {
        $this->markTestIncomplete('User should be found from JWT in request');
    }
}
