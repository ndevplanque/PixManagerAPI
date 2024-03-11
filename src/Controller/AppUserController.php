<?php

namespace App\Controller;

use App\Entity\AppUser;
use App\Form\AppUserType;
use App\Repository\AppUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users')]
class AppUserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(AppUserRepository $appUserRepository): JsonResponse
    {
        return $this->json([
            'users' => $appUserRepository->findAll(),
        ]);
    }

    #[Route('/', name: 'app_user_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $appUser = new AppUser();
        $form = $this->createForm(AppUserType::class, $appUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($appUser);
            $entityManager->flush();

            return $this->json([
                'app_user' => $appUser,
                'form' => $form,
            ]);
        }

        return $this->json([
            'app_user' => $appUser,
            'form' => $form,
        ], 400);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(AppUser $appUser): JsonResponse
    {
        return $this->json(['user' => $appUser]);
    }

    #[Route('/{id}', name: 'app_user_edit', methods: ['PUT'])]
    public function edit(Request $request, AppUser $appUser, EntityManagerInterface $entityManager): JsonResponse
    {
        $form = $this->createForm(AppUserType::class, $appUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->json([
                'user' => $appUser,
                'form' => $form,
            ]);
        }

        return $this->json([
            'user' => $appUser,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(Request $request, AppUser $appUser, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($this->isCsrfTokenValid('delete' . $appUser->getId(), $request->request->get('_token'))) {
            $entityManager->remove($appUser);
            $entityManager->flush();
            return $this->json([], 204);
        }

        return $this->json([], 400);
    }
}
