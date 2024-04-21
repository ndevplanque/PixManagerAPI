<?php

namespace App\Controller;

use App\Entity\AppUser;
use App\Entity\Photo;
use App\Service\Label\LabelDeleteService;
use App\Service\Photo\PhotoDeleteService;
use App\Service\Photo\PhotoListingByUserService;
use App\Utils\JsonHelper;
use App\Utils\RequestHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class AdminController extends AbstractController
{
    public function __construct(
        private readonly JsonHelper    $jsonHelper,
        private readonly RequestHelper $requestHelper,
    )
    {
    }

    #[Route('/api/admin/photos/users/{id}', name: 'adminListPhotosByUserId', methods: ['GET'])]
    #[OA\Get(
        path: '/api/admin/photos/users/{id}',
        description: 'List all photos by user ID for admins',
        tags: ['Admin'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Photos listed successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Photo')
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized access',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedResponse'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response500'
                )
            ),
        ]
    )]
    public function adminListPhotosByUserId(
        Request                   $request,
        AppUser                   $user,
        PhotoListingByUserService $photoListingByUserService,
    ): JsonResponse
    {
        $this->requestHelper->getUser($request)->shouldBeAdmin();

        $listingByUserResponse = $photoListingByUserService->handle($request);

        return $this->jsonHelper->send(
            json_encode($listingByUserResponse->jsonSerialize())
        );
    }

    #[Route('/api/admin/labels/{name}', name: 'adminDeleteLabel', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/admin/labels/{name}',
        description: 'Delete a label by name for admins',
        tags: ['Admin'],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Label deleted successfully',
                content: new OA\JsonContent(
                    type: 'string',
                    example: ''
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized access',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedResponse'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response500'
                )
            ),
        ]
    )]
    public function adminDeleteLabel(
        Request            $request,
        string             $name,
        LabelDeleteService $labelDeleteService,
    ): JsonResponse
    {
        $this->requestHelper->getUser($request)->shouldBeAdmin();

        $labelDeleteService->handle($name);

        return $this->jsonHelper->noContent();
    }

    #[Route('/api/users/{id}', name: 'adminDeleteUser', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/users/{id}',
        description: 'Delete a user by ID for admins',
        tags: ['Admin'],
        responses: [
            new OA\Response(
                response: 204,
                description: 'User deleted successfully',
                content: new OA\JsonContent(
                    type: 'string',
                    example: ''
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized access',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedResponse'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response500'
                )
            ),
        ]
    )]
    public function adminDeleteUser(
        Request                $request,
        AppUser                $user,
        EntityManagerInterface $entityManager,
    ): JsonResponse
    {
        $this->requestHelper->getUser($request)->shouldBeAdmin();

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->jsonHelper->noContent();
    }

    #[Route('/api/admin/photos/{id}', name: 'adminDeletePhoto', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/admin/photos/{id}',
        description: 'Delete a specific photo by ID for admins',
        tags: ['Admin'],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Photo deleted successfully',
                content: new OA\JsonContent(
                    type: 'string',
                    example: ''
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized access',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/UnauthorizedResponse'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Response500'
                )
            ),
        ]
    )]
    public function adminDeletePhoto(
        Request            $request,
        Photo              $photo,
        PhotoDeleteService $photoDeleteService,
    ): JsonResponse
    {
        $this->requestHelper->getUser($request)->shouldBeAdmin();

        $photoDeleteService->handle($photo);

        return $this->jsonHelper->noContent();
    }
}
