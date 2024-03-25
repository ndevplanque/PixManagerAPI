<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Photo;
use App\Service\Album\AlbumEnsureGetService;
use App\Service\Photo\PhotoCreateService;
use App\Service\Photo\PhotoDeleteService;
use App\Service\Photo\PhotoListingByAlbumService;
use App\Service\Photo\PhotoListingByLabelService;
use App\Service\Photo\PhotoListingByUserService;
use App\Service\Photo\PhotoUpdateService;
use App\Utils\FileHelper;
use App\Utils\JsonHelper;
use App\Utils\RequestHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PhotoController extends AbstractController
{
    public function __construct(
        private readonly JsonHelper    $jsonHelper,
        private readonly FileHelper    $fileHelper,
        private readonly RequestHelper $requestHelper,
    )
    {
    }

    #[Route('/api/photos/albums/{id}', name: 'listPhotosByAlbumId', methods: ['GET'])]
    public function listPhotosByAlbumId(
        Request                    $request,
        Album                      $album,
        PhotoListingByAlbumService $photoListingByAlbumService,
    ): JsonResponse
    {
        $this->requestHelper->getUser($request)->shouldBe($album->getOwner());

        $listingByAlbumResponse = $photoListingByAlbumService->handle($album);

        return $this->jsonHelper->send(
            json_encode($listingByAlbumResponse->jsonSerialize())
        );
    }

    #[Route('/api/photos/labels/{labelName}', name: 'listPhotosByLabelName', methods: ['GET'])]
    public function listPhotosByLabelName(
        Request                    $request,
        string                     $labelName,
        PhotoListingByLabelService $photoListingByLabelService,
    ): JsonResponse
    {
        $user = $this->requestHelper->getUser($request);

        $listingByLabelResponse = $photoListingByLabelService->handle($user, $labelName);

        return $this->jsonHelper->send(
            json_encode($listingByLabelResponse->jsonSerialize())
        );
    }

    #[Route('/api/photos/albums/{id}', name: "createPhotoInAlbum", methods: ['POST'])]
    public function createPhotoInAlbum(
        Request            $request,
        Album              $album,
        PhotoCreateService $photoCreateService,
    ): JsonResponse
    {
        $this->requestHelper->getUser($request)->shouldHaveAccessToAlbum($album);

        $photoResponse = $photoCreateService->handle($request, $album);

        return $this->jsonHelper->created(
            json_encode($photoResponse->jsonSerialize())
        );
    }

    #[Route('/api/photos', name: "createPhoto", methods: ['POST'])]
    public function createPhoto(
        Request               $request,
        AlbumEnsureGetService $albumEnsureGetService,
        PhotoCreateService    $photoCreateService,
    ): JsonResponse
    {
        $user = $this->requestHelper->getUser($request);

        $album = $albumEnsureGetService->handle($user);

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
        $this->requestHelper->getUser($request)->shouldBe($photo->getOwner());

        $photoResponse = $photoUpdateService->handle($request, $photo);

        return $this->jsonHelper->created(
            json_encode($photoResponse->jsonSerialize())
        );
    }

    #[Route('/api/photos/{id}', name: 'deletePhoto', methods: ['DELETE'])]
    public function deletePhoto(
        Request            $request,
        Photo              $photo,
        PhotoDeleteService $photoDeleteService,
    ): JsonResponse
    {
        $this->requestHelper->getUser($request)->shouldBe($photo->getOwner());

        $photoDeleteService->handle($photo);

        return $this->jsonHelper->noContent();
    }

    #[Route('/api/photos/{id}', name: 'getPhotoFile', methods: ['GET'])]
    public function getPhotoFile(
        Request $request,
        Photo   $photo,
    ): BinaryFileResponse
    {
        $this->requestHelper->getUser($request)->shouldHaveAccessToPhoto($photo);

        return $this->fileHelper->sendPhoto($photo);
    }

    #[Route('/api/photos', name: 'listPhoto', methods: ['GET'])]
    public function listPhoto(
        Request $request,
    ): JsonResponse
    {
        throw new HttpException(500, 'Not implemented.');

        $user = $this->requestHelper->getUser($request);
        $search = $this->requestHelper->getQueryParam($request, 'search');

        return $this->jsonHelper->send('');
    }
}
