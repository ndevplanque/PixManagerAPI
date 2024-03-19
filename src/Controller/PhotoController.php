<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\AppUser;
use App\Entity\Photo;
use App\Service\Photo\PhotoCreateService;
use App\Service\Photo\PhotoDeleteService;
use App\Service\Photo\PhotoListingByAlbumService;
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
use Symfony\Component\Serializer\SerializerInterface;

class PhotoController extends AbstractController
{
    public function __construct(
        private readonly JsonHelper $jsonHelper,
        private readonly FileHelper $fileHelper,
    )
    {
    }

    #[Route('/api/photos/albums/{id}', name: 'listPhotosByAlbum', methods: ['GET'])]
    public function listPhotosByAlbum(
        Album                      $album,
        PhotoListingByAlbumService $photoListingByAlbumService,
        SerializerInterface        $serializer,
    ): JsonResponse
    {
        $user = $album->getOwner();
        // todo: security check -> requester should be $user
        $photos = $photoListingByAlbumService->handle($album);
        $json = $serializer->serialize($photos, 'json', ['groups' => ['photos']]);
        return $this->jsonHelper->send($json);
    }

    /**
     * @throws Exception
     */
    #[Route('/api/photos/users/{id}', name: 'listPhotosByUser', methods: ['GET'])]
    public function listPhotosByUser(
        AppUser                   $user,
        PhotoListingByUserService $photoListingByUserService,
        SerializerInterface       $serializer,
    ): JsonResponse
    {
        // todo: security check -> requester should be $user
        $photos = $photoListingByUserService->handle($user);
        $json = $serializer->serialize($photos, 'json', ['groups' => ['photos']]);
        return $this->jsonHelper->send($json);
    }

    #[Route('/api/photos/albums/{id}', name: "createPhoto", methods: ['POST'])]
    public function createPhoto(
        Request             $request,
        Album               $album,
        PhotoCreateService  $photoCreateService,
    ): JsonResponse
    {
        $user = $album->getOwner();
        // todo: security check -> requester should be $user
        $photo = $photoCreateService->handle($request, $album);
        $json = json_encode($photo->jsonSerialize());
        return $this->jsonHelper->created($json);
    }

    #[Route('/api/photos/{id}', name: "updatePhoto", methods: ['PUT'])]
    public function updatePhoto(
        Request             $request,
        Photo               $photo,
        PhotoUpdateService  $photoUpdateService,
        SerializerInterface $serializer,
    ): JsonResponse
    {
        $user = $photo->getOwner();
        // todo: security check -> requester should be $user
        $photo = $photoUpdateService->handle($request, $photo);
        $json = $serializer->serialize($photo, 'json', ['groups' => 'photos']);
        return $this->jsonHelper->created($json);
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
