<?php

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
use Symfony\Component\Serializer\SerializerInterface;

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
        SerializerInterface $serializer,
    ): JsonResponse
    {
        $labels = $labelListingService->handle();
        $json = json_encode($labels->jsonSerialize());
        return $this->jsonHelper->send($json);
    }

    #[Route('/api/labels', name: "createLabel", methods: ['POST'])]
    public function createLabel(
        Request             $request,
        LabelCreateService  $labelCreateService,
        SerializerInterface $serializer,
    ): JsonResponse
    {
        $label = $labelCreateService->handle($request);
        $json = $serializer->serialize($label, 'json', ['groups' => ['labels']]);
        return $this->jsonHelper->created($json);
    }

    /**
     * @throws Exception
     */
    #[Route('/api/labels', name: 'deleteLabel', methods: ['DELETE'])]
    public function deleteLabel(
        Request            $request,
        LabelDeleteService $labelDeleteService,
    ): JsonResponse
    {
        $labelDeleteService->handle($request);

        return $this->jsonHelper->noContent();
    }
}
