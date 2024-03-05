<?php

namespace App\Controller;

use App\Services\PhotoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class PhotoController extends AbstractController
{
    #[Route('/photos', name: 'photo_list')]
    public function index(PhotoService $service): JsonResponse
    {
        return $this->json([
            'items' => $service->list(),
        ]);
    }
}
