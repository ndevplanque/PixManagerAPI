<?php

namespace App\Controller;

use App\Entity\AppUser;
use App\Repository\AppUserRepository;
use App\Utils\JsonHelper;
use App\Utils\PasswordHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AppUserController extends AbstractController
{
    private readonly JsonHelper $jsonHelper;
    private readonly PasswordHelper $passwordHelper;

    public function __construct()
    {
        $this->jsonHelper = new JsonHelper();
        $this->passwordHelper = new PasswordHelper();
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

    #[Route('/api/users', name: "createUser", methods: ['POST'])]
    public function createUser(
        Request                $request,
        SerializerInterface    $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface  $urlGenerator,
    ): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), AppUser::class, 'json');
        $user->setRoles(['ROLE_USER']);

        if ($user->isAdmin() === null) {
            $user->setIsAdmin(false);
            $user->setRoles(
                array_merge($user->getRoles(), ['ROLE_ADMIN'])
            );
        }

        $user->setPassword($this->passwordHelper->hash($user->getPassword()));

        $entityManager->persist($user);
        $entityManager->persist($user->newAlbum());
        $entityManager->flush();

        $json = $serializer->serialize($user, 'json', ['groups' => 'users']);

        // si on voulait rediriger vers une page détaillant le user
        $location = null; // $urlGenerator->generate('detailUser', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->jsonHelper->created($json, $location);
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
