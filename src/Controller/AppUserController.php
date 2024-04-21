<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AppUser;
use App\Repository\AppUserRepository;
use App\Utils\JsonHelper;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Utils\RequestHelper;
use OpenApi\Attributes as OA;

class AppUserController extends AbstractController
{
    public function __construct(
        private readonly JsonHelper    $jsonHelper,
        private readonly RequestHelper $requestHelper,

    )
    {
    }
    /**
     * This request returns information about the current logged-in user.
     */
    #[Route('/api/getMe', name: 'getMyself', methods: ['GET'])]
    #[OA\Get(
        path: '/api/getMe',
        description: 'Get information about the current logged-in user',
        tags: ['Users'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User information retrieved successfully',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/AppUser'
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
    #[Security(name: 'Bearer')]
    public function getMyself(
        Request             $request,
        AppUserRepository   $userRepository,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $user = $this->requestHelper->getUser($request); // Assuming requestHelper is defined elsewhere
        $email = $user->getEmail();
        $response = $userRepository->findOneBy(['email' => $email]); // Assuming you expect only one user to match the email
        $jsonData = $serializer->serialize($response, 'json', ['groups' => ['users']]);

        return new JsonResponse($jsonData, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * This request returns user information by ID.
     */
    #[Route('/api/users/{id}', name: 'getUserById', methods: ['GET'])]
    #[OA\Get(
        path: '/api/users/{id}',
        description: 'Get user information by ID',
        tags: ['Users'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User information retrieved successfully by ID',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/AppUser'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid request',
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
            ),
        ]
    )]
    #[Security(name: 'Bearer')]
    public function getUserById(
        AppUser             $user,
        SerializerInterface $serializer,
    ): JsonResponse
    {
        $json = $serializer->serialize($user, 'json', ['groups' => ['users']]);
        return $this->jsonHelper->send($json);
    }

    /**
     * This request lists all users.
     */
    #[Route('/api/users', name: 'listUsers', methods: ['GET'])]
    #[OA\Get(
        path: '/api/users',
        description: 'List all users',
        tags: ['Users'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of all users',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/AppUser')
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid request',
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
            ),
        ]
    )]
    #[Security(name: 'Bearer')]
    public function listUsers(
        AppUserRepository   $userRepository,
        SerializerInterface $serializer,
    ): JsonResponse
    {
        $users = $userRepository->findAll();
        $json = $serializer->serialize($users, 'json', ['groups' => ['users']]);
        return $this->jsonHelper->send($json);
    }
}
