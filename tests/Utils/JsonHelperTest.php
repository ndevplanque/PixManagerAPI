<?php

namespace Tests\App\Utils;

use App\Utils\JsonHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonHelperTest extends TestCase
{
    private readonly JsonHelper $jsonHelper;

    public function setUp(): void
    {
        $this->jsonHelper = new JsonHelper();
    }

    public function testSend(): void
    {
        $expected = new JsonResponse(
            data: $json = '{"key":"value"}',
            status: Response::HTTP_OK,
            json: true
        );

        $this->assertEquals($expected, $this->jsonHelper->send($json));
    }

    public function testCreatedWithNoLocation(): void
    {
        $expected = new JsonResponse(
            data: $json = '{"key":"value"}',
            status: Response::HTTP_CREATED,
            headers: [],
            json: true
        );

        $this->assertEquals($expected, $this->jsonHelper->created($json));
    }

    public function testCreated(): void
    {
        $expected = new JsonResponse(
            data: $json = '{"key":"value"}',
            status: Response::HTTP_CREATED,
            headers: ["Location" => $location = '/url/de/ma/page'],
            json: true
        );

        $this->assertEquals($expected, $this->jsonHelper->created($json, $location));
    }

    public function testNoContent(): void
    {
        $expected = new JsonResponse(status: Response::HTTP_NO_CONTENT);

        $this->assertEquals($expected, $this->jsonHelper->noContent());
    }
}
