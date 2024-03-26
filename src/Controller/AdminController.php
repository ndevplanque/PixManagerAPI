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

class AdminController extends AbstractController
{
    public function __construct(
        private readonly JsonHelper    $jsonHelper,
        private readonly RequestHelper $requestHelper,
    )
    {
    }

    #[Route('/api/admin/photos/users/{id}', name: 'adminListPhotosByUserId', methods: ['GET'])]
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
