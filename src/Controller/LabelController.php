<?php

namespace App\Controller;

use App\Entity\Label;
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
    private readonly JsonHelper $jsonHelper;

    public function __construct(JsonHelper $jsonHelper)
    {
        $this->jsonHelper = $jsonHelper;
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
    #[Route('/api/labels/{id}', name: 'deletePhoto', methods: ['DELETE'])]
    public function deleteLabel(
        Label              $label,
        LabelDeleteService $labelDeleteService,
    ): JsonResponse
    {
        $labelDeleteService->handle($label);

        return $this->jsonHelper->noContent();
    }
}
