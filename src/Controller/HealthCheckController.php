<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HealthCheckController extends AbstractController
{
    #[Route('/api/health-check', name: 'HealthCheck')]
    public function index(): JsonResponse
    {
        return $this->json(['success' => true]);
    }
}
