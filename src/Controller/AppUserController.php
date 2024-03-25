<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AppUser;
use App\Repository\AppUserRepository;
use App\Utils\JsonHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AppUserController extends AbstractController
{
    public function __construct(
        private readonly JsonHelper $jsonHelper
    ) {
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

    #[Route('/api/users/{id}', name: 'deleteUser', methods: ['DELETE'])]
    public function deleteUser(
        AppUser                $user,
        EntityManagerInterface $entityManager,
    ): JsonResponse
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->jsonHelper->noContent();
    }
}
