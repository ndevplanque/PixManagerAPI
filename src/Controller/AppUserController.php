<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AppUser;
use App\Repository\AppUserRepository;
use App\Utils\JsonHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Utils\RequestHelper;

class AppUserController extends AbstractController
{
    public function __construct(
        private readonly JsonHelper $jsonHelper,
        private readonly RequestHelper  $requestHelper,

    ) {
    }

#[Route('/api/getMe', name: 'getMyself', methods: ['GET'])]
public function getMyself(
    Request             $request,
    AppUserRepository   $userRepository,
    SerializerInterface $serializer
): JsonResponse {
    $user = $this->requestHelper->getUser($request); // Assuming requestHelper is defined elsewhere
    $email = $user->getEmail();
    $response = $userRepository->findOneBy(['email' => $email]); // Assuming you expect only one user to match the email
    $jsonData = $serializer->serialize($response, 'json', ['groups' => ['users']]);

    return new JsonResponse($jsonData, JsonResponse::HTTP_OK, [], true);
}

    #[Route('/api/users/{id}', name: 'getUserById', methods: ['GET'])]
    public function getUserById(
        AppUser             $user,
        SerializerInterface $serializer,
    ): JsonResponse
    {
        $json = $serializer->serialize($user, 'json', ['groups' => ['users']]);
        return $this->jsonHelper->send($json);
    }

    #[Route('/api/users', name: 'listUsers', methods: ['GET'])]
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
