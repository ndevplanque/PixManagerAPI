<?php

namespace Tests\App\Response;

use App\Entity\Label;
use App\Response\LabelResponse;
use PHPUnit\Framework\TestCase;

class LabelResponseTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $response = new LabelResponse(
            $this->createConfiguredMock(Label::class, [
                'getId' => 2,
                'getName' => 'cats',
            ])
        );

        $this->assertEquals('cats', $response->jsonSerialize());
    }

}
