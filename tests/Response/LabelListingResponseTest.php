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
            $this->createConfiguredMock(Label::class, [
                'getId' => 1,
                'getName' => 'dogs',
            ]),
            $this->createConfiguredMock(Label::class, [
                'getId' => 2,
                'getName' => 'cats',
            ]),
            $this->createConfiguredMock(Label::class, [
                'getId' => 3,
                'getName' => 'cute',
            ]),
        ]);

        $expected = ['labels' => [
            'dogs',
            'cats',
            'cute',
        ]];

        $this->assertEquals($expected, $response->jsonSerialize());
    }

}
