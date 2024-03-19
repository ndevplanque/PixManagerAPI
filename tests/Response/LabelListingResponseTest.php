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
                'getName' => 'chien',
            ]),
            $this->createConfiguredMock(Label::class, [
                'getId' => 2,
                'getName' => 'chat',
            ]),
            $this->createConfiguredMock(Label::class, [
                'getId' => 3,
                'getName' => 'macron',
            ]),
        ]);

        $expected = ['labels' => [
            ['id' => 1, 'name' => 'chien'],
            ['id' => 2, 'name' => 'chat'],
            ['id' => 3, 'name' => 'macron'],
        ]];

        $this->assertEquals($expected, $response->jsonSerialize());
    }

}
