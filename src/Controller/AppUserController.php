<?php

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
    private readonly JsonHelper $jsonHelper;

    public function __construct()
    {
        $this->jsonHelper = new JsonHelper();
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

    #[Route('/api/users', name: 'getUsers', methods: ['GET'])]
    public function getUsers(
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
