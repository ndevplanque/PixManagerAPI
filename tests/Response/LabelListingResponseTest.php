<?php

namespace Tests\App\Response;

use App\Entity\Label;
use App\Response\LabelListingResponse;
use PHPUnit\Framework\TestCase;

class LabelListingResponseTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $response = new LabelListingResponse([
            $this->createConfiguredMock(Label::class, ['getName' => 'chien']),
            $this->createConfiguredMock(Label::class, ['getName' => 'chat']),
            $this->createConfiguredMock(Label::class, ['getName' => 'macron']),
        ]);

        $expected = ['labels' => ['chien', 'chat', 'macron']];

        $this->assertEquals($expected, $response->jsonSerialize());
    }

}
