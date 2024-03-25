<?php

namespace App\Controller;

use App\Entity\AppUser;
use App\Utils\JsonHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'app_user')]
class SecurityController extends AbstractController
{
    public function __construct(
        private readonly JsonHelper $jsonHelper,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     *  Crée un nouvel utilisateur à partir d'une requête HTTP.
     *  Valide l'utilisateur, hache son mot de passe, persiste l'utilisateur et son nouvel album dans la base de données.
     */
    #[Route('/register', name: 'app_user_register', methods: ['POST'])]
    public function createUser(
        Request                $request,
        SerializerInterface    $serializer,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher
    ): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), AppUser::class, 'json');
        $user->setRoles(['ROLE_USER']);

        $password = $user->getPassword();

        if ($user->isAdmin() === null) {
            $user->setIsAdmin(false);
        } elseif ($user->isAdmin() === true) {
            $user->setRoles(
                array_merge($user->getRoles(), ['ROLE_ADMIN'])
            );
        }

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $hashedPassword= $hasher->hashPassword($user, $password);

        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->persist($user->newAlbum());
        $entityManager->flush();

        $json = $serializer->serialize($user, 'json', ['groups' => 'users']);

        return $this->jsonHelper->created($json);
    }

    /**
     * Met à jour le mot de passe de l'utilisateur à partir d'une requête HTTP.
     * Vérifie les informations d'identification de l'utilisateur, hache le nouveau mot de passe,
     * et persiste les modifications dans la base de données.
     */
    #[Route('/password', name: 'app_user_password', methods: ['PUT'])]
    public function updatePassword(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $oldPassword = $data['oldPassword'];
        $newPassword = $data['newPassword'];

        $userRepository = $entityManager->getRepository(AppUser::class);
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user || !$hasher->isPasswordValid($user, $oldPassword)) {
            return new JsonResponse(['errors' => ['Invalid credentials']], Response::HTTP_BAD_REQUEST);
        }

        $hashedPassword = $hasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        $token = $tokenStorage->getToken();
        if ($token) {
            $jwtToken = $token->getCredentials();
            return new JsonResponse(['token' => $jwtToken, 'message' => 'Password updated successfully'], Response::HTTP_OK);
        } else {
            return new JsonResponse(['message' => 'No active JWT token found. User is not authenticated.'], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Information : Route inutile pour le moment mais elle pourrait servir (ne pas supprimer)
     * Traite la requête de connexion de l'utilisateur.
     * Dé-sérialise les données JSON de la requête en un objet AppUser,
     * puis vérifie les informations d'identification fournies.
     * Si les informations sont valides, retourne les données de l'utilisateur au format JSON.
    */
//    #[Route('/login', name: 'app_user_login', methods: ['POST'])]
//    public function login(
//        Request $request,
//        SerializerInterface $serializer,
//        UserPasswordHasherInterface $hasher,
//        AppUserRepository $appUserRepository
//    ): JsonResponse {
//        $user = $serializer->deserialize($request->getContent(), AppUser::class, 'json');
//
//        $email = $user->getEmail();
//        $password = $user->getPassword();
//
//        $existingUser = $appUserRepository->findOneBy(['email' => $email]);
//
//        if (!$existingUser || !$hasher->isPasswordValid($existingUser, $password)) {
//            return new JsonResponse(['errors' => ['Invalid credentials']], Response::HTTP_BAD_REQUEST);
//        }
//
//        // Ajouter le code pour retourner le JWT
//
//        $json = $serializer->serialize($existingUser, 'json', ['groups' => 'users']);
//
//        return new JsonResponse(['user' => $json], Response::HTTP_OK);
//    }

}
