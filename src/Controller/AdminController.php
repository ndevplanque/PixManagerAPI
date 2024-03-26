<?php

namespace App\Controller;

use App\Entity\AppUser;
use App\Service\Label\LabelDeleteService;
use App\Service\Photo\PhotoListingByUserService;
use App\Utils\JsonHelper;
use App\Utils\RequestHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    public function __construct(
        private readonly JsonHelper    $jsonHelper,
        private readonly RequestHelper $requestHelper,
    )
    {
    }

    #[Route('/api/admin/photos/users/{id}', name: 'listPhotosByUserId', methods: ['GET'])]
    public function listPhotosByUserId(
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

    #[Route('/api/admin/labels/{name}', name: 'deleteLabel', methods: ['DELETE'])]
    public function deleteLabel(
        Request            $request,
        string             $name,
        LabelDeleteService $labelDeleteService,
    ): JsonResponse
    {
        $this->requestHelper->getUser($request)->shouldBeAdmin();

        $labelDeleteService->handle($name);

        return $this->jsonHelper->noContent();
    }

    #[Route('/api/users/{id}', name: 'deleteUser', methods: ['DELETE'])]
    public function deleteUser(
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
}
