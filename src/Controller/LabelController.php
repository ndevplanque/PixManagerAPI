<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Label\LabelCreateService;
use App\Service\Label\LabelListingService;
use App\Utils\JsonHelper;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;


class LabelController extends AbstractController
{
    public function __construct(
        private readonly JsonHelper $jsonHelper,
    )
    {
    }
    /**
     * This request return a list of labels.
     */
    #[OA\Get(
        path: '/api/labels',
        description: 'Get a list of labels',
        tags: ['Labels'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Returns a list of labels',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Label')
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response500'
                )
            )
        ]
    )]
    #[Security(name: 'Bearer')]
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
    /**
     * This request creates a new label.
     */
    #[OA\Post(
        path: '/api/labels',
        description: 'Create a new label',
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    ref: '#/components/schemas/Label'
                )
            )
        ),
        tags: ['Labels'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Label created successfully',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Label'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response400'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response500'
                )
            )
        ]
    )]
    #[Security(name: 'Bearer')]
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
}
