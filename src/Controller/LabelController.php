<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Label\LabelCreateService;
use App\Service\Label\LabelDeleteService;
use App\Service\Label\LabelListingService;
use App\Utils\JsonHelper;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class LabelController extends AbstractController
{
    public function __construct(
        private readonly JsonHelper $jsonHelper,
    )
    {
    }

    #[Route('/api/labels', name: 'listLabels', methods: ['GET'])]
    public function listLabels(
        LabelListingService $labelListingService,
    ): JsonResponse
    {
        $labelListingResponse = $labelListingService->handle();

        return $this->jsonHelper->send(
            json_encode($labelListingResponse->jsonSerialize())
        );
    }

    #[Route('/api/labels', name: "createLabel", methods: ['POST'])]
    public function createLabel(
        Request            $request,
        LabelCreateService $labelCreateService,
    ): JsonResponse
    {
        $labelResponse = $labelCreateService->handle($request);

        return $this->jsonHelper->created(
            json_encode(['label' => $labelResponse])
        );
    }

    /**
     * @throws Exception
     */
    #[Route('/api/labels/{name}', name: 'deleteLabel', methods: ['DELETE'])]
    public function deleteLabel(
        string             $name,
        LabelDeleteService $labelDeleteService,
    ): JsonResponse
    {
        $labelDeleteService->handle($name);

        return $this->jsonHelper->noContent();
    }
}
