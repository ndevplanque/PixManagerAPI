<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\AppUser;
use App\Entity\Label;
use App\Entity\Photo;
use App\Service\Photo\PhotoCreateService;
use App\Service\Photo\PhotoDeleteService;
use App\Service\Photo\PhotoListingByAlbumService;
use App\Service\Photo\PhotoListingByLabelService;
use App\Service\Photo\PhotoListingByUserService;
use App\Service\Photo\PhotoUpdateService;
use App\Utils\FileHelper;
use App\Utils\JsonHelper;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PhotoController extends AbstractController
{
    public function __construct(
        private readonly JsonHelper $jsonHelper,
        private readonly FileHelper $fileHelper,
    )
    {
    }

    #[Route('/api/photos/albums/{id}', name: 'listPhotosByAlbumId', methods: ['GET'])]
    public function listPhotosByAlbumId(
        Album                      $album,
        PhotoListingByAlbumService $photoListingByAlbumService,
    ): JsonResponse
    {
        $user = $album->getOwner();
        // todo: security check -> requester should be $user

        $listingByAlbumResponse = $photoListingByAlbumService->handle($album);

        return $this->jsonHelper->send(
            json_encode($listingByAlbumResponse->jsonSerialize())
        );
    }

    /**
     * @throws Exception
     */
    #[Route('/api/photos/users/{id}', name: 'listPhotosByUserId', methods: ['GET'])]
    public function listPhotosByUserId(
        AppUser                   $user,
        PhotoListingByUserService $photoListingByUserService,
    ): JsonResponse
    {
        // todo: security check -> requester should be $user
        $listingByUserResponse = $photoListingByUserService->handle($user);

        return $this->jsonHelper->send(
            json_encode($listingByUserResponse->jsonSerialize())
        );
    }

    #[Route('/api/photos/labels/{id}', name: 'listPhotosByLabelId', methods: ['GET'])]
    public function listPhotosByLabelId(
        Label                      $label,
        PhotoListingByLabelService $photoListingByLabelService,
    ): JsonResponse
    {
        // todo: security check -> requester should be $user

        $listingByLabelResponse = $photoListingByLabelService->handle($label);

        return $this->jsonHelper->send(
            json_encode($listingByLabelResponse->jsonSerialize())
        );
    }

    #[Route('/api/photos/albums/{id}', name: "createPhoto", methods: ['POST'])]
    public function createPhoto(
        Request            $request,
        Album              $album,
        PhotoCreateService $photoCreateService,
    ): JsonResponse
    {
        $user = $album->getOwner();
        // todo: security check -> requester should be $user

        $photoResponse = $photoCreateService->handle($request, $album);

        return $this->jsonHelper->created(
            json_encode($photoResponse->jsonSerialize())
        );
    }

    #[Route('/api/photos/{id}', name: "updatePhoto", methods: ['PUT'])]
    public function updatePhoto(
        Request            $request,
        Photo              $photo,
        PhotoUpdateService $photoUpdateService,
    ): JsonResponse
    {
        $user = $photo->getOwner();
        // todo: security check -> requester should be $user

        $photoResponse = $photoUpdateService->handle($request, $photo);

        return $this->jsonHelper->created(
            json_encode($photoResponse->jsonSerialize())
        );
    }

    /**
     * @throws Exception
     */
    #[Route('/api/photos/{id}', name: 'deletePhoto', methods: ['DELETE'])]
    public function deletePhoto(
        Photo              $photo,
        PhotoDeleteService $photoDeleteService,
    ): JsonResponse
    {
        $photoDeleteService->handle($photo);

        return $this->jsonHelper->noContent();
    }

    #[Route('/api/photos/{id}', name: 'getPhotoFile', methods: ['GET'])]
    public function getPhotoFile(
        Photo $photo
    ): BinaryFileResponse
    {
        // todo: check that this user does owns the photo or it belongs to a shared album
        return $this->fileHelper->sendPhoto($photo);
    }
}
