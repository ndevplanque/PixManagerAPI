<?php

namespace App\Tests\Controller;

use App\Controller\HealthCheckController;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class HealthCheckControllerTest extends TestCase
{
    private readonly HealthCheckController $controller;

    public function setUp(): void
    {
        $this->controller = new HealthCheckController();
        $this->controller->setContainer($this->createMock(ContainerInterface::class));
    }

    public function testIndex(): void
    {
        $expected = json_encode(['success' => true]);

        $this->assertEquals($expected, $this->controller->index()->getContent());
    }
}
